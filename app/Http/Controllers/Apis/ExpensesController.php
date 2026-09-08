<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Expense;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ExpensesController extends Controller
{
    public function index()
    {
        try {
            $expenses = Expense::with('account')->get();

            return $this->json_response('success', 'Expenses', 'Expenses fetched successfully', 200, $expenses);
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'account_id' => 'nullable|exists:accounts,id',
                'account_name' => 'nullable|min:2',
                'amount' => 'required|numeric|min:0',
                'expense_date' => 'required',
                'reference' => 'nullable',
            ]);

            if ($validator->fails()) {
                return $this->json_response('error', 'Validation failed', $validator->errors(), 422);
            }

            // If account_id is provided, auto-fetch account name
            $accountName = $request->account_name;
            if ($request->account_id) {
                $account = Account::find($request->account_id);
                $accountName = $account->name;
            }

            $expense = Expense::create([
                'group_id' => Auth::user()->group_id,
                'account_id' => $request->account_id,
                'account_name' => $accountName,
                'amount' => $request->amount,
                'expense_date' => strtotime($request->expense_date),
                'reference' => $request->reference,
                'created_by' => Auth::id(),
            ]);

            return $this->json_response('success', 'Expense Created', 'Expense created successfully', 200, $expense);
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function show($id)
    {
        try {
            $expense = Expense::with('account')->findOrFail($id);

            return $this->json_response('success', 'Expense', 'Expense fetched successfully', 200, $expense);
        } catch (ModelNotFoundException $e) {
            return $this->json_response('error', 'Not Found', 'Expense not found', 404);
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $expense = Expense::find($id);
            if (! $expense) {
                return $this->json_response('error', 'Not Found', 'Expense not found', 404);
            }

            $validator = Validator::make($request->all(), [
                'account_id' => 'sometimes|exists:accounts,id',
                'account_name' => 'sometimes|min:2',
                'amount' => 'sometimes|numeric|min:0',
                'expense_date' => 'sometimes',
                'reference' => 'sometimes',
            ]);
            if ($validator->fails()) {
                return $this->json_response('error', 'Validation failed', $validator->errors(), 422);
            }

            $expense->account_id = $request->account_id ?? $expense->account_id;

            // If account_id is provided, auto-fetch account name
            if ($request->account_id) {
                $account = Account::find($request->account_id);
                $expense->account_name = $account->name;
            } else {
                $expense->account_name = $request->account_name ?? $expense->account_name;
            }
            $expense->amount = $request->amount ?? $expense->amount;
            $expense->expense_date = $request->expense_date ? strtotime($request->expense_date) : $expense->expense_date;
            $expense->reference = $request->reference ?? $expense->reference;
            $expense->save();

            return $this->json_response('success', 'Expense Updated', 'Expense updated successfully', 200, $expense);
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $expense = Expense::find($id);
            if (! $expense) {
                return $this->json_response('error', 'Not Found', 'Expense not found', 404);
            }

            $expense->delete();

            return $this->json_response('success', 'Expense Deleted', 'Expense deleted successfully', 200);
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }
}