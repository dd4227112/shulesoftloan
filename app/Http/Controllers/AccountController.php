<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountType;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class AccountController extends Controller
{

    public function index()
    {
        if (\Auth::user()->can('manage account')) {
            $accounts = Account::where('parent_id', parentId())->get();
            return view('account.index', compact('accounts'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if (\Auth::user()->can('create account')) {
            $customers = User::where('parent_id', parentId())->where('type', 'customer')->get()->pluck('name', 'id');
            $customers->prepend(__('Select Customer'), '');
            $type = AccountType::where('parent_id', parentId())->get()->pluck('title', 'id');
            $type->prepend(__('Select Account Type'), '');
            $status = Account::$status;
            return view('account.create', compact('type', 'customers', 'status'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function store(Request $request)
    {
        if (\Auth::user()->can('create account')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'customer' => 'required',
                    'account_type' => 'required',
                    'status' => 'required',
                    'balance' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $account = new Account();
            $account->account_number = $this->accountNumber();
            $account->customer = $request->customer;
            $account->customer = $request->customer;
            $account->account_type = $request->account_type;
            $account->status = $request->status;
            $account->balance = $request->balance;
            $account->notes = $request->notes;
            $account->parent_id = parentId();
            $account->save();

            $module = 'account_create';
            $notification = Notification::where('parent_id', parentId())->where('module', $module)->first();
            $setting = settings();
            $errorMessage = '';
            if (!empty($notification) && $notification->enabled_email == 1) {
                $notification_responce = MessageReplace($notification, $account->id);
                $datas = [
                    'subject' => $notification_responce['subject'],
                    'message' => $notification_responce['message'],
                    'module'  => $module,
                    'logo'    => $setting['company_logo'],
                ];


                $to = $account->Customers->email;
                $response = commonEmailSend($to, $datas);
                if ($response['status'] == 'error') {
                    $errorMessage = $response['message'];
                }
            }


            return redirect()->route('account.index')->with('success', __('Account successfully created.').'</br>'.$errorMessage);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function show(Account $account)
    {
        //
    }


    public function edit(Account $account)
    {
        if (\Auth::user()->can('edit account')) {
            $customers = User::where('parent_id', parentId())->where('type', 'customer')->get()->pluck('name', 'id');
            $customers->prepend(__('Select Customer'), '');
            $type = AccountType::where('parent_id', parentId())->get()->pluck('title', 'id');
            $type->prepend(__('Select Account Type'), '');
            $status = Account::$status;
            return view('account.edit', compact('account', 'type', 'customers', 'status'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request, Account $account)
    {
        if (\Auth::user()->can('edit account')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'customer' => 'required',
                    'account_type' => 'required',
                    'status' => 'required',
                    'balance' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $account->customer = $request->customer;
            $account->customer = $request->customer;
            $account->account_type = $request->account_type;
            $account->status = $request->status;
            $account->balance = $request->balance;
            $account->notes = $request->notes;
            $account->save();

            return redirect()->route('account.index')->with('success', __('Account successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy(Account $account)
    {
        if (\Auth::user()->can('delete account')) {
            $account->delete();
            return redirect()->route('account.index')->with('success', __('Account successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function accountNumber()
    {
        $latestAccount = Account::where('parent_id', parentId())->latest()->first();
        if ($latestAccount == null) {
            return 1;
        } else {
            return $latestAccount->account_number + 1;
        }
    }
}
