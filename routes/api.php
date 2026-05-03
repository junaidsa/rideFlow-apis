<?php

use App\Http\Controllers\Apis\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::post('/register',[AuthenticationController::class,'register']);
Route::post('/login',[AuthenticationController::class,'authenticate']);

Route::group(['middleware' =>['auth:sanctum']], function () {
    });

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
