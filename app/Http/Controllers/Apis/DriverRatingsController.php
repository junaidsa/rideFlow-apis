<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\DriverRating;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DriverRatingsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = DriverRating::query();

            // Optional filter: /driver-ratings?driver_id=5
            if ($request->filled('driver_id')) {
                $query->where('driver_id', $request->driver_id);
            }

            $ratings = $query->with('driver:id,name,phone')->get();

            return $this->json_response('success', 'Driver Ratings', 'Driver ratings fetched successfully', 200, $ratings);
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'driver_id' => 'required|exists:accounts,id',
                'rating' => 'required|integer|between:1,5',
            ]);

            if ($validator->fails()) {
                return $this->json_response('error', 'Validation failed', $validator->errors(), 422);
            }

            $rating = DriverRating::create([
                'group_id' => Auth::user()->group_id,
                'driver_id' => $request->driver_id,
                'rating' => $request->rating,
                'created_by' => Auth::id(),
            ]);

            return $this->json_response('success', 'Rating Added', 'Driver rating added successfully', 200, $rating);
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function show($id)
    {
        try {
            $rating = DriverRating::with('driver:id,name,phone')->findOrFail($id);

            return $this->json_response('success', 'Driver Rating', 'Driver rating fetched successfully', 200, $rating);
        } catch (ModelNotFoundException $e) {
            return $this->json_response('error', 'Not Found', 'Rating not found', 404);
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $rating = DriverRating::find($id);
            if (! $rating) {
                return $this->json_response('error', 'Not Found', 'Rating not found', 404);
            }

            $rating->delete();
            return $this->json_response('success', 'Rating Deleted', 'Driver rating deleted successfully', 200);
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    /**
     * Driver ka average stars summary.
     * GET /drivers/{id}/ratings-summary
     */
    public function driverSummary($driverId)
    {
        try {
            $driver = Account::find($driverId);
            if (! $driver) {
                return $this->json_response('error', 'Not Found', 'Driver not found', 404);
            }

            $summary = DriverRating::where('driver_id', $driverId)
                ->selectRaw('COUNT(*) as total_ratings, COALESCE(AVG(rating), 0) as average_rating')
                ->first();

            return $this->json_response('success', 'Driver Rating Summary', 'Driver rating summary fetched successfully', 200, [
                'driver_id' => $driver->id,
                'driver_name' => $driver->name,
                'total_ratings' => (int) $summary->total_ratings,
                'average_rating' => round((float) $summary->average_rating, 2),
            ]);
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }
}