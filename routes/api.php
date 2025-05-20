<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/viewuser', [UserController::class, 'viewUser']);
Route::post('/login', [UserController::class ,'loginPost']);
Route::post('/register', [UserController::class ,'registrationPost']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
});



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
