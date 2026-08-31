<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CarsController extends Controller
{
    public function index()
    {
        try {
            $cars = Car::get();

            return $this->json_response('success', 'Cars', 'Cars fetched successfully', 200, $cars->toArray());
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'model' => 'required',
                'color' => 'required',
                'plate_number' => 'required|unique:cars,plate_number',
            ]);

            if ($validator->fails()) {
                return $this->json_response('error', 'Validation failed', $validator->errors(), 422);
            }

            $car = Car::create([
                'group_id' => Auth::user()->group_id,
                'name' => $request->name,
                'model' => $request->model,
                'color' => $request->color,
                'plate_number' => $request->plate_number,
                'created_by' => Auth::id(),
            ]);

            return $this->json_response('success', 'Car Created', 'Car created successfully', 200, $car->toArray());
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function show($id)
    {
        try {
            $car = Car::findOrFail($id);

            return $this->json_response('success', 'Car', 'Car fetched successfully', 200, $car->toArray());
        } catch (ModelNotFoundException $e) {
            return $this->json_response('error', 'Not Found', 'Car not found', 404);
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $car = Car::find($id);
            if (! $car) {
                return $this->json_response('error', 'Not Found', 'Car not found', 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|min:2',
                'model' => 'nullable',
                'color' => 'nullable',
                'plate_number' => 'nullable',
            ]);
            if ($validator->fails()) {
                return $this->json_response('error', 'Validation failed', $validator->errors(), 422);
            }

            $car->name = $request->name ?? $car->name;
            $car->model = $request->model ?? $car->model;
            $car->color = $request->color ?? $car->color;
            $car->plate_number = $request->plate_number ?? $car->plate_number;
            $car->save();

            return $this->json_response('success', 'Car Updated', 'Car updated successfully', 200, $car->toArray());
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $car = Car::findOrFail($id);
            $car->deleted_by = Auth::id();
            $car->save();
            $car->delete();
            return $this->json_response('success','Car Deleted','Car deleted successfully',200);
        } catch (ModelNotFoundException $e) {
            return $this->json_response('error','Not Found','Car not found',404);

        } catch (\Throwable $e) {
            return $this->json_response('error','Something went wrong',['message' => $e->getMessage(),'line' => $e->getLine(),'file' => $e->getFile()],500);
        }
    }
}
