<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

Route::post('/sensor', [App\Http\Controllers\Api\SensorController::class, 'store']);
Route::get('/sensor', [App\Http\Controllers\Api\SensorController::class, 'index']);

Route::post('/counter', [App\Http\Controllers\Api\CounterController::class, 'store']);
Route::get('/counter', [App\Http\Controllers\Api\CounterController::class, 'index']);
Route::get('/counter/hourly', [App\Http\Controllers\Api\CounterController::class, 'indexhourly']);
Route::get('/reset-check', function (Request $request) {
    $deviceId = $request->device_id;
    $shouldReset = Cache::get("reset:$deviceId", false);

    return response($shouldReset ? 'reset' : 'ok', 200)
        ->header('Content-Type', 'text/plain');
});
Route::post('/trigger-reset', function () {
    Cache::put("reset:esp32-001", true, now()->addMinutes(1));
    return response()->json(['status' => 'reset flag set']);
});
