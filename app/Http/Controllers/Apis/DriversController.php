<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DriversController extends Controller
{
    public function index()
    {
        try {
            $accounts = Account::get();

            return $this->json_response('success', 'Drivers', 'Drivers fetched successfully', 200, $accounts->toArray());
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'phone' => 'nullable',
                'father_phone' => 'nullable',
                'cnic' => 'nullable',
                'account_type' => 'nullable',
                'address' => 'nullable',
            ]);

            if ($validator->fails()) {
                return $this->json_response('error', 'Validation failed', $validator->errors(), 422);
            }

            $account = Account::create([
                'group_id' => Auth::user()->group_id,
                'name' => $request->name,
                'phone' => $request->phone,
                'father_phone' => $request->father_phone,
                'cnic' => $request->cnic,
                'account_type' => $request->account_type,
                'address' => $request->address,
                'created_by' => Auth::id(),
            ]);

            return $this->json_response('success', 'Driver Created', 'Driver created successfully', 200, $account->toArray());
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function show($id)
    {
        try {
            $account = Account::findOrFail($id);

            return $this->json_response('success', 'Driver', 'Driver fetched successfully', 200, $account->toArray());
        } catch (ModelNotFoundException $e) {
            return $this->json_response('error', 'Not Found', 'Driver not found', 404);
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $account = Account::find($id);
            if (! $account) {
                return $this->json_response('error', 'Not Found', 'Driver not found', 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|min:2',
                'phone' => 'nullable',
                'father_phone' => 'nullable',
                'cnic' => 'nullable',
                'account_type' => 'nullable',
                'address' => 'nullable',
            ]);
            if ($validator->fails()) {
                return $this->json_response('error', 'Validation failed', $validator->errors(), 422);
            }

            $account->name = $request->name ?? $account->name;
            $account->phone = $request->phone ?? $account->phone;
            $account->father_phone = $request->father_phone ?? $account->father_phone;
            $account->cnic = $request->cnic ?? $account->cnic;
            $account->account_type = $request->account_type ?? $account->account_type;
            $account->address = $request->address ?? $account->address;
            $account->save();

            return $this->json_response('success', 'Driver Updated', 'Driver updated successfully', 200, $account->toArray());
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $account = Account::findOrFail($id);
            $account->deleted_by = Auth::id();
            $account->save();
            $account->delete();
            return $this->json_response('success', 'Driver Deleted', 'Driver deleted successfully', 200);
        } catch (ModelNotFoundException $e) {
            return $this->json_response('error', 'Not Found', 'Driver not found', 404);
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }
}