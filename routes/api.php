<?php

use App\Http\Controllers\Apis\AuthenticationController;
use App\Http\Controllers\Apis\CarsController;
use App\Http\Controllers\Apis\DriversController;
use App\Http\Controllers\Apis\RidesController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// Authentication
Route::post('/register', [AuthenticationController::class, 'register']);
Route::post('/login', [AuthenticationController::class, 'authenticate']);

// Refresh application
Route::get('/refresh', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');
    Artisan::call('migrate', ['--force' => true]);
    return 'Cache cleared and migrations run successfully!';
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    // Cars
    Route::get('/cars', [CarsController::class, 'index']);
    Route::post('/cars', [CarsController::class, 'store']);
    Route::get('/cars/{id}', [CarsController::class, 'show']);
    Route::put('/cars/{id}', [CarsController::class, 'update']);
    Route::delete('/cars/{id}', [CarsController::class, 'destroy']);

    // Drivers
    Route::get('/drivers', [DriversController::class, 'index']);
    Route::post('/drivers', [DriversController::class, 'store']);
    Route::get('/drivers/{id}', [DriversController::class, 'show']);
    Route::put('/drivers/{id}', [DriversController::class, 'update']);
    Route::delete('/drivers/{id}', [DriversController::class, 'destroy']);

    // Rides
    Route::get('/rides', [RidesController::class, 'index']);
    Route::post('/rides', [RidesController::class, 'store']);
    Route::get('/rides/{id}', [RidesController::class, 'show']);
    Route::put('/rides/{id}', [RidesController::class, 'update']);
    Route::delete('/rides/{id}', [RidesController::class, 'destroy']);
});