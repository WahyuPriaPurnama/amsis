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



Route::apiResource('/posts', PostController::class);



Route::post('/counter', [App\Http\Controllers\Api\CounterController::class, 'store']);
Route::get('/counter', [App\Http\Controllers\Api\CounterController::class, 'index']);
Route::get('/counter/{range}', [App\Http\Controllers\Api\CounterController::class, 'indexhourly']);
