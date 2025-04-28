<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('manage expense')) {
            $expenses = Expense::where('parent_id', parentId())->get();
            return view('expense.index', compact('expenses'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create expense')) {
            return view('expense.create');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function store(Request $request)
    {

        if (\Auth::user()->can('create expense')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'date' => 'required',
                    'amount' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $expense = new Expense();
            $expense->title = $request->title;
            $expense->amount = $request->amount;
            $expense->date = $request->date;
            $expense->notes = $request->notes;
            $expense->parent_id = parentId();
            if ($request->hasFile('attachment')) {
                $extension = $request->file('attachment')->getClientOriginalExtension();
                $name = \Str::uuid() . '.' . $extension;
                // $photo = $request->file('attachment')->storeAs('upload/expense', $name);
                $photo = $request->file('attachment')->storeAs('upload/expense', $name, 'public');

                $expense->attachment = $name;
            }
            $expense->save();

            return redirect()->route('expense.index')->with('success', __('Expense successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function show(Expense $expense)
    {
        //
    }


    public function edit(Expense $expense)
    {
        if (\Auth::user()->can('edit expense')) {
            return view('expense.edit', compact('expense'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request, Expense $expense)
    {

        if (\Auth::user()->can('edit expense')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'date' => 'required',
                    'amount' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $expense->title = $request->title;
            $expense->amount = $request->amount;
            $expense->date = $request->date;
            $expense->notes = $request->notes;
            $expense->parent_id = parentId();
            if ($request->hasFile('attachment')) {
                $extension = $request->file('attachment')->getClientOriginalExtension();
                $name = \Str::uuid() . '.' . $extension;
                $photo = $request->file('attachment')->storeAs('upload/expense', $name);
                $expense->attachment = $name;
            }
            $expense->save();
            return redirect()->route('expense.index')->with('success', __('Expense successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy(Expense $expense)
    {
        if (\Auth::user()->can('delete expense')) {
            $expense->delete();
            return redirect()->route('expense.index')->with('success', __('Expense successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
