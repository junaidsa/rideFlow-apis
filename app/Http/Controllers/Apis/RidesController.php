<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\Ride;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RidesController extends Controller
{
    public function index()
    {
        try {
            $rides = Ride::with(['driver', 'car'])->get();

            return $this->json_response('success', 'Rides', 'Rides fetched successfully', 200, $rides->toArray());
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'driver_id' => 'required|exists:accounts,id',
                'car_id' => 'required|exists:cars,id',
                'name' => 'required|min:2',
                'amount' => 'required|numeric|min:0',
                'date' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->json_response('error', 'Validation failed', $validator->errors(), 422);
            }

            $ride = Ride::create([
                'group_id' => Auth::user()->group_id,
                'driver_id' => $request->driver_id,
                'car_id' => $request->car_id,
                'name' => $request->name,
                'amount' => $request->amount,
                'date' => strtotime($request->date),
                'created_by' => Auth::id(),
            ]);

            return $this->json_response('success', 'Ride Created', 'Ride created successfully', 200, $ride->toArray());
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function show($id)
    {
        try {
            $ride = Ride::with(['driver', 'car'])->findOrFail($id);

            return $this->json_response('success', 'Ride', 'Ride fetched successfully', 200, $ride->toArray());
        } catch (ModelNotFoundException $e) {
            return $this->json_response('error', 'Not Found', 'Ride not found', 404);
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $ride = Ride::find($id);
            if (! $ride) {
                return $this->json_response('error', 'Not Found', 'Ride not found', 404);
            }

            $validator = Validator::make($request->all(), [
                'driver_id' => 'sometimes|exists:accounts,id',
                'car_id' => 'sometimes|exists:cars,id',
                'name' => 'sometimes|min:2',
                'amount' => 'sometimes|numeric|min:0',
                'date' => 'sometimes',
            ]);
            if ($validator->fails()) {
                return $this->json_response('error', 'Validation failed', $validator->errors(), 422);
            }
            
            $ride->driver_id = $request->driver_id ?? $ride->driver_id;
            $ride->car_id = $request->car_id ?? $ride->car_id;
            $ride->name = $request->name ?? $ride->name;
            $ride->amount = $request->amount ?? $ride->amount;
            $ride->date = $request->date ? strtotime($request->date) : $ride->date;
            $ride->save();

            return $this->json_response('success', 'Ride Updated', 'Ride updated successfully', 200, $ride->toArray());
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $ride = Ride::findOrFail($id);
            $ride->deleted_by = Auth::id();
            $ride->save();
            $ride->delete();
            return $this->json_response('success', 'Ride Deleted', 'Ride deleted successfully', 200);
        } catch (ModelNotFoundException $e) {
            return $this->json_response('error', 'Not Found', 'Ride not found', 404);
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }
}