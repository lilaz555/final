<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
Route::get('/viewuser', [UserController::class, 'viewUser']);
Route::post('/login', [UserController::class ,'loginPost']);
Route::post('/register', [UserController::class ,'registrationPost']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
});*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
});

Route::get('/viewuser', [UserController::class, 'viewUser']);



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
