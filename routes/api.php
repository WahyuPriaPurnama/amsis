<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\PostController;
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



Route::post('/login', LoginController::class);
Route::apiResource('/posts', PostController::class);
Route::middleware('jwt.auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Api\DashboardController::class, 'index']);
    Route::get('/log-activity', [\App\Http\Controllers\Api\DashboardController::class, 'logActivity']);
    Route::delete('/log-activity', [\App\Http\Controllers\Api\DashboardController::class, 'truncate']);
    Route::post('/logout', [\App\Http\Controllers\Api\LogoutController::class, 'logout']);
});
