<?php

use App\Http\Controllers\Apis\AuthenticationController;
use App\Http\Controllers\Apis\CarsController;
use Illuminate\Support\Facades\Route;

Route::post('/register',[AuthenticationController::class,'register']);
Route::post('/login',[AuthenticationController::class,'authenticate']);

Route::group(['middleware' =>['auth:sanctum']], function () {
    Route::get('/cars',[CarsController::class,'index']);
    Route::post('/cars',[CarsController::class,'store']);
    Route::get('/cars/{id}',[CarsController::class,'show']);
    Route::put('/cars/{id}',[CarsController::class,'update']);
    Route::delete('/cars/{id}',[CarsController::class,'destroy']);
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');