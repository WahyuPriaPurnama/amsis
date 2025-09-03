<?php

use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('/posts', PostController::class);
Route::post('/login', \App\Http\Controllers\Api\LoginController::class);
Route::post('/logout', App\Http\Controllers\Api\LogoutController::class);
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
