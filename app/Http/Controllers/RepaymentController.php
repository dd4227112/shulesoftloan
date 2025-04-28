<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Loan;
use App\Models\Notification;
use App\Models\Repayment;
use App\Models\RepaymentSchedule;
use App\Models\User;
use Illuminate\Http\Request;

class RepaymentController extends Controller
{

    public function index()
    {
        if (\Auth::user()->can('manage repayment')) {
            $repayments = Repayment::where('parent_id', parentId())->orderBy('payment_date','asc')->get();
            return view('repayments.index', compact('repayments'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    public function schedules()
    {
        if (\Auth::user()->can('manage repayment')) {
            $schedules = RepaymentSchedule::where('parent_id', parentId())->orderBy('due_date','asc')->get();
            return view('repayments.schedule', compact('schedules'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create repayment')) {
            $loan = Loan::where('parent_id', parentId())->get();
            $loans = [];
            $loans['']=__('Select Loan');
            foreach ($loan as $key => $value) {
                $loans[$value->id] = loanPrefix() . $value->loan_id;
            }

            return view('repayments.create', compact('loans'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function store(Request $request)
    {

        if (\Auth::user()->can('create repayment')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'loan_id' => 'required',
                    'payment_date' => 'required',
                    'principal_amount' => 'required',
                    'interest' => 'required',
                    'penality' => 'required',
                    'total_amount' => 'required',

                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $repayment = new Repayment();
            $repayment->loan_id = $request->loan_id;
            $repayment->payment_date = $request->payment_date;
            $repayment->principal_amount = $request->principal_amount;
            $repayment->interest = $request->interest;
            $repayment->penality = $request->penality;
            $repayment->total_amount = $request->total_amount;
            $repayment->parent_id = parentId();
            $repayment->save();
            $installment = RepaymentSchedule::where('loan_id', $request->loan_id)->where('id', $request->schedule_id)->orderBy('created_at', 'DESC')->first();
            if ($installment) {
                $installment->status = 'Paid';
                $installment->save();
            }

            $module = 'repayment_create';
            $notification = Notification::where('parent_id', parentId())->where('module', $module)->first();
            $setting = settings();
            $errorMessage = '';
            if (!empty($notification) && $notification->enabled_email == 1) {
                $notification_responce = MessageReplace($notification, $repayment->id);
                $datas = [
                    'subject' => $notification_responce['subject'],
                    'message' => $notification_responce['message'],
                    'module'  => $module,
                    'logo'    => $setting['company_logo'],
                ];

                $customer=Customer::where('user_id',$repayment->Loans->customer)->first();
                $customerEmail = User::find($repayment->Loans->customer)->email;
                $branchEmail = $customer->branch->email;
                $to = [$customerEmail, $branchEmail];
                $response = commonEmailSend($to, $datas);
                if ($response['status'] == 'error') {
                    $errorMessage = $response['message'];
                }
            }

            return redirect()->route('repayment.index')->with('success', __('Repayment successfully created.').'</br>'.$errorMessage);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function show(Repayment $repayment)
    {
        //
    }


    public function edit(Repayment $repayment)
    {
        if (\Auth::user()->can('edit repayment')) {
            $loan = Loan::where('parent_id', parentId())->get();
            $loans = [];
            $loans['']=__('Select Loan');
            foreach ($loan as $key => $value) {
                $loans[$value->id] = loanPrefix() . $value->loan_id;
            }

            return view('repayments.edit', compact('repayment', 'loans'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function update(Request $request, Repayment $repayment)
    {
        if (\Auth::user()->can('create repayment')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'loan_id' => 'required',
                    'payment_date' => 'required',
                    'principal_amount' => 'required',
                    'interest' => 'required',
                    'penality' => 'required',
                    'total_amount' => 'required',

                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $repayment->loan_id = $request->loan_id;
            $repayment->payment_date = $request->payment_date;
            $repayment->principal_amount = $request->principal_amount;
            $repayment->interest = $request->interest;
            $repayment->penality = $request->penality;
            $repayment->total_amount = $request->total_amount;
            $repayment->save();

            return redirect()->back()->with('success', __('Repayment successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy(Repayment $repayment)
    {
        if (\Auth::user()->can('delete repayment')) {
            $repayment->delete();

            return redirect()->back()->with('success', __('Repayment successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    public function getLoanInstallment(Request $request)
    {
        // $installment = RepaymentSchedule::where('loan_id', $request->loan)->where('status', 'Pending')->orderBy('created_at', 'DESC')->first();
        $installment = RepaymentSchedule::where('loan_id', $request->loan)->where('status', 'Pending')->first();
        if ($installment) {
            $installment->toArray();
            $response = [
                'status' => true,
                'installment' => $installment,
            ];
        } else {
            $response = [
                'status' => false,
            ];
        }
        return response()->json($response);
    }

    public function scheduleDestroy($id)
    {
        if (\Auth::user()->can('delete repayment')) {
            RepaymentSchedule::find($id)->delete();
            return redirect()->back()->with('success', __('Repayment successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
}
