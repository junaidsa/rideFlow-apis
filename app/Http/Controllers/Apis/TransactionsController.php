<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TransactionsController extends Controller
{
    public function types()
    {
        try {
            $types = config('transaction_types.types');

            return $this->json_response('success', 'Transaction Types', 'Transaction types fetched successfully', 200, $types);
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function index()
    {
        try {
            $transactions = Transaction::with(['ride', 'account'])->get();

            return $this->json_response('success', 'Transactions', 'Transactions fetched successfully', 200, $transactions->toArray());
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'login_id' => 'nullable|exists:users,id',
                'ride_id' => 'nullable|exists:rides,id',
                'account_id' => 'nullable|exists:accounts,id',
                'type' => 'required|min:2',
                'reference' => 'nullable|min:2',
                'amount' => 'required|numeric|min:0',
                'transaction_date' => 'required',
                'is_reconcile' => 'sometimes|in:0,1',
            ]);

            if ($validator->fails()) {
                return $this->json_response('error', 'Validation failed', $validator->errors(), 422);
            }

            $transaction = Transaction::create([
                'group_id' => Auth::user()->group_id,
                'login_id' => $request->login_id,
                'ride_id' => $request->ride_id,
                'account_id' => $request->account_id,
                'type' => $request->type,
                'reference' => $request->reference,
                'amount' => $request->amount,
                'transaction_date' => strtotime($request->transaction_date),
                'is_reconcile' => $request->is_reconcile ?? 0,
                'created_by' => Auth::id(),
            ]);

            return $this->json_response('success', 'Transaction Created', 'Transaction created successfully', 200, $transaction->toArray());
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function show($id)
    {
        try {
            $transaction = Transaction::with(['ride', 'account'])->findOrFail($id);

            return $this->json_response('success', 'Transaction', 'Transaction fetched successfully', 200, $transaction->toArray());
        } catch (ModelNotFoundException $e) {
            return $this->json_response('error', 'Not Found', 'Transaction not found', 404);
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $transaction = Transaction::find($id);
            if (! $transaction) {
                return $this->json_response('error', 'Not Found', 'Transaction not found', 404);
            }

            $validator = Validator::make($request->all(), [
                'login_id' => 'sometimes|exists:users,id',
                'ride_id' => 'sometimes|exists:rides,id',
                'account_id' => 'sometimes|exists:accounts,id',
                'type' => 'sometimes|min:2',
                'reference' => 'sometimes|min:2',
                'amount' => 'sometimes|numeric|min:0',
                'transaction_date' => 'sometimes',
                'is_reconcile' => 'sometimes|in:0,1',
            ]);
            if ($validator->fails()) {
                return $this->json_response('error', 'Validation failed', $validator->errors(), 422);
            }

            $transaction->login_id = $request->login_id ?? $transaction->login_id;
            $transaction->ride_id = $request->ride_id ?? $transaction->ride_id;
            $transaction->account_id = $request->account_id ?? $transaction->account_id;
            $transaction->type = $request->type ?? $transaction->type;
            $transaction->reference = $request->reference ?? $transaction->reference;
            $transaction->amount = $request->amount ?? $transaction->amount;
            $transaction->transaction_date = $request->transaction_date ? strtotime($request->transaction_date) : $transaction->transaction_date;
            $transaction->is_reconcile = $request->is_reconcile ?? $transaction->is_reconcile;
            $transaction->save();

            return $this->json_response('success', 'Transaction Updated', 'Transaction updated successfully', 200, $transaction->toArray());
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);
            $transaction->deleted_by = Auth::id();
            $transaction->save();
            $transaction->delete();

            return $this->json_response('success', 'Transaction Deleted', 'Transaction deleted successfully', 200);
        } catch (ModelNotFoundException $e) {
            return $this->json_response('error', 'Not Found', 'Transaction not found', 404);
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }
}
