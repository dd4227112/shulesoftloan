<?php

namespace App\Http\Controllers;

use App\Models\LoanType;
use Illuminate\Http\Request;

class LoanTypeController extends Controller
{

    public function index()
    {
        if (\Auth::user()->can('manage loan type')) {
            $loanTypes = LoanType::where('parent_id', \Auth::user()->id)->get();
            return view('loan_type.index', compact('loanTypes'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        $interestType=LoanType::$interestType;
        $termPeroid=LoanType::$termPeroid;
        return view('loan_type.create',compact('interestType','termPeroid'));
    }


    public function store(Request $request)
    {
        if (\Auth::user()->can('create loan type') ) {
            $validator = \Validator::make(
                $request->all(), [
                    'type' => 'required',
                    'min_loan_amount' => 'required',
                    'max_loan_amount' => 'required',
                    'interest_type' => 'required',
                    'interest_rate' => 'required',
                    'max_loan_term' => 'required',
                    'loan_term_period' => 'required',
                    'penalties' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $loanType = new LoanType();
            $loanType->type = $request->type;
            $loanType->min_loan_amount = $request->min_loan_amount;
            $loanType->max_loan_amount = $request->max_loan_amount;
            $loanType->interest_type = $request->interest_type;
            $loanType->interest_rate = $request->interest_rate;
            $loanType->max_loan_term = $request->max_loan_term;
            $loanType->loan_term_period = $request->loan_term_period;
            $loanType->penalties = $request->penalties;
            $loanType->status = 1;
            $loanType->notes = $request->notes;
            $loanType->parent_id = parentId();
            $loanType->save();

            return redirect()->back()->with('success', __('Loan Type successfully created.'));

        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function show(LoanType $loanType)
    {
        return view('loan_type.show',compact('loanType'));
    }


    public function edit(LoanType $loanType)
    {
        $interestType=LoanType::$interestType;
        $termPeroid=LoanType::$termPeroid;
        return view('loan_type.edit',compact('interestType','loanType','termPeroid'));
    }


    public function update(Request $request, LoanType $loanType)
    {
        if (\Auth::user()->can('edit loan type') ) {
            $validator = \Validator::make(
                $request->all(), [
                    'type' => 'required',
                    'min_loan_amount' => 'required',
                    'max_loan_amount' => 'required',
                    'interest_type' => 'required',
                    'interest_rate' => 'required',
                    'max_loan_term' => 'required',
                    'loan_term_period' => 'required',
                    'penalties' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }


            $loanType->type = $request->type;
            $loanType->min_loan_amount = $request->min_loan_amount;
            $loanType->max_loan_amount = $request->max_loan_amount;
            $loanType->interest_type = $request->interest_type;
            $loanType->interest_rate = $request->interest_rate;
            $loanType->max_loan_term = $request->max_loan_term;
            $loanType->loan_term_period = $request->loan_term_period;
            $loanType->penalties = $request->penalties;
            $loanType->status = 1;
            $loanType->notes = $request->notes;
            $loanType->save();

            return redirect()->back()->with('success', __('Loan Type successfully updated.'));

        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy(LoanType $loanType)
    {
        if (\Auth::user()->can('delete loan type') ) {
            $loanType->delete();
            return redirect()->back()->with('success', 'Loan Type successfully deleted.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
