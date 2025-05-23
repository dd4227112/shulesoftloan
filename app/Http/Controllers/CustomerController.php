<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Loan;
use App\Models\Notification;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Spatie\Permission\Models\Role;

class CustomerController extends Controller
{

    public function index()
    {
        if (\Auth::user()->can('manage customer')) {
            $customers = User::where('parent_id', parentId())->where('type', 'customer')->get();
            return view('customer.index', compact('customers'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function create()
    {
        if (\Auth::user()->can('create customer')) {
            $customerNumber = $this->customerNumber();
            $gender = Customer::$gender;
            $maritalStatus = Customer::$maritalStatus;
            $branch=Branch::where('parent_id',parentId())->get()->pluck('name','id');
            $branch->prepend(__('Select Branch'),'');
            $documentTypes = DocumentType::where('parent_id', parentId())->get()->pluck('title', 'id');
            $documentTypes->prepend(__('Select Document Type'), '');
            $document_status = Loan::$document_status;
            return view('customer.create', compact('customerNumber', 'gender', 'maritalStatus','branch','documentTypes','document_status'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function store(Request $request)
    {
        if (\Auth::user()->can('create customer')) {

            $validator = \Validator::make(
                $request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|unique:users',
                    'phone_number' => 'required',
                    'branch_id' => 'required',
                    'gender' => 'required',
                    'dob' => 'required',
                    'marital_status' => 'required',
                    'profession' => 'required',
                    'city' => 'required',
                    'state' => 'required',
                    'country' => 'required',
                    'zip_code' => 'required',
                    'address' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first())->withInput();
            }

            $ids = parentId();
            $authUser = \App\Models\User::find($ids);
            $totalCustomer = $authUser->totalCustomer();
            $subscription = Subscription::find($authUser->subscription);
            if ($totalCustomer >= $subscription->customer_limit && $subscription->customer_limit != 0) {
                return redirect()->back()->with('error', __('Your customer limit is over, please upgrade your subscription.'));
            }
            $userRole = Role::where('parent_id', parentId())->where('name', 'customer')->first();
            $customer = new User();
            $customer->name = $request->name;
            $customer->email = $request->email;
            $customer->phone_number = $request->phone_number;
            $customer->password = \Hash::make($request->password);
            $customer->type = $userRole->name;
            $customer->profile = 'avatar.png';
            $customer->lang = 'english';
            $customer->parent_id = parentId();
            $customer->save();
            $customer->assignRole($userRole);
            if (!empty($customer)) {
                $customerDetail = new Customer();
                $customerDetail->user_id = $customer->id;
                $customerDetail->customer_id = $this->customerNumber();
                $customerDetail->dob = $request->dob;
                $customerDetail->gender = $request->gender;
                $customerDetail->branch_id = $request->branch_id;
                $customerDetail->marital_status = $request->marital_status;
                $customerDetail->profession = $request->profession;
                $customerDetail->company = $request->company;
                $customerDetail->city = $request->city;
                $customerDetail->state = $request->state;
                $customerDetail->country = $request->country;
                $customerDetail->zip_code = $request->zip_code;
                $customerDetail->address = $request->address;
                $customerDetail->notes = $request->notes;
                $customerDetail->parent_id = parentId();
                $customerDetail->save();

                foreach ($request->document_type as $key => $value) {
                    if ($value) {
                        $customerDocument = new Document();
                        $customerDocument->customer_id = $customer->id;
                        $customerDocument->document_type = $request->document_type[$key];
                        $customerDocument->status = $request->document_status[$key];
                        $customerDocument->notes = ($request->description[$key]) ? $request->description[$key] : '';
                        if (isset($request->document[$key])) {
                            $extension = $request->document[$key]->getClientOriginalExtension();
                            $name = \Str::uuid() . '.' . $extension;
                            // $photo = $request->document[$key]->storeAs('upload/customer_document', $name);
                            $photo = $request->document[$key]->storeAs('upload/customer_document', $name, 'public');
                            $customerDocument->document = $name;
                        }
                        $customerDocument->save();
                    }
                }
            }


            $module = 'customer_create';
            $notification = Notification::where('parent_id', parentId())->where('module', $module)->first();
            $setting = settings();
            $errorMessage = '';
            if (!empty($notification) && $notification->enabled_email == 1) {
                $notification_responce = MessageReplace($notification, $customerDetail->id);
                $datas = [
                    'subject' => $notification_responce['subject'],
                    'message' => $notification_responce['message'],
                    'module'  => $module,
                    'logo'    => $setting['company_logo'],
                ];

                $customerEmail = $request->email;
                $branchEmail = $customerDetail->branch->email;
                $to = [$customerEmail, $branchEmail];
                $response = commonEmailSend($to, $datas);
                if ($response['status'] == 'error') {
                    $errorMessage = $response['message'];
                }
            }

            return redirect()->route('customer.index')->with('success', __('Customer successfully created.').'</br>'.$errorMessage);
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function show($ids)
    {
        if (\Auth::user()->can('show customer')) {
            $id = Crypt::decrypt($ids);
            $customer = User::find($id);
            return view('customer.show', compact('customer'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function edit($ids)
    {
        if (\Auth::user()->can('edit customer')) {
            $id = Crypt::decrypt($ids);
            $customer = User::find($id);
            $gender = Customer::$gender;
            $maritalStatus = Customer::$maritalStatus;
            $branch=Branch::where('parent_id',parentId())->get()->pluck('name','id');
            $branch->prepend(__('Select Branch'),'');
            $documentTypes = DocumentType::where('parent_id', parentId())->get()->pluck('title', 'id');
            $documentTypes->prepend(__('Select Document Type'), '');
            $document_status = Loan::$document_status;
            return view('customer.edit', compact('customer', 'gender', 'maritalStatus','branch','documentTypes','document_status'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function update(Request $request, $id)
    {

        if (\Auth::user()->can('edit customer')) {

            $validator = \Validator::make(
                $request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email,' . $id,
                    'phone_number' => 'required',
                    'branch_id' => 'required',
                    'gender' => 'required',
                    'dob' => 'required',
                    'marital_status' => 'required',
                    'profession' => 'required',
                    'city' => 'required',
                    'state' => 'required',
                    'country' => 'required',
                    'zip_code' => 'required',
                    'address' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }


            $customer = User::find($id);

            $ids = [];
            if(!empty($customer->Documents)){
                foreach ($customer->Documents as $key => $value) {
                    $ids[$value->id] = $value->id;
                }
            }


            $customer->name = $request->name;
            $customer->email = $request->email;
            $customer->phone_number = $request->phone_number;
            $customer->save();
            if (!empty($customer)) {
                $customerDetail = Customer::where('user_id', $customer->id)->first();
                $customerDetail->dob = $request->dob;
                $customerDetail->gender = $request->gender;
                $customerDetail->branch_id = $request->branch_id;
                $customerDetail->marital_status = $request->marital_status;
                $customerDetail->profession = $request->profession;
                $customerDetail->company = $request->company;
                $customerDetail->city = $request->city;
                $customerDetail->state = $request->state;
                $customerDetail->country = $request->country;
                $customerDetail->zip_code = $request->zip_code;
                $customerDetail->address = $request->address;
                $customerDetail->notes = $request->notes;
                $customerDetail->save();

                foreach ($request->document_type as $key => $value) {

                    if (isset($request->id[$key]) && in_array($request->id[$key], $ids)) {
                        $customerDocument = Document::find($request->id[$key]);
                        $customerDocument->document_type = $request->document_type[$key];
                        $customerDocument->status = $request->document_status[$key];
                        $customerDocument->notes = $request->description[$key];

                        if ($request->document && isset($request->document[$key])) {
                            $extension = $request->document[$key]->getClientOriginalExtension();
                            $name = \Str::uuid() . '.' . $extension;
                            $photo = $request->document[$key]->storeAs('upload/customer_document', $name);
                            $customerDocument->document = $name;
                        }
                        $customerDocument->save();
                        unset($ids[$request->id[$key]]);
                    } else {
                        if ($value) {

                            $customerDocument = new Document();
                            $customerDocument->customer_id = $customer->id;
                            $customerDocument->document_type = $request->document_type[$key];
                            $customerDocument->status = $request->document_status[$key];
                            $customerDocument->notes = $request->description[$key];
                            if ($request->document && $request->document[$key]) {
                                $extension = $request->document[$key]->getClientOriginalExtension();
                                $name = \Str::uuid() . '.' . $extension;
                                $photo = $request->document[$key]->storeAs('upload/customer_document', $name);
                                $customerDocument->document = $name;
                            }
                            $customerDocument->save();
                        }
                    }
                }

                if (count($ids) > 0) {
                    foreach ($ids as $key => $id) {
                        if ($id) {
                            $customerDocument = Document::find($id);
                            if ($customerDocument) {
                                $customerDocument->delete();
                            }
                        }
                    }
                }
            }
            return redirect()->route('customer.index')->with('success', __('Customer successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function destroy($id)
    {
        if (\Auth::user()->can('delete customer')) {
            $customer = User::find($id);
            Customer::where('user_id', $customer->id)->delete();
            $customer->delete();

            return redirect()->route('customer.index')->with('success', __('Customer successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function customerNumber()
    {
        $latestCustomer = Customer::where('parent_id', parentId())->latest()->first();
        if ($latestCustomer == null) {
            return 1;
        } else {
            return $latestCustomer->customer_id + 1;
        }
    }
}
