<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
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

Route::prefix('auth')->group(function(){
    Route::post('/login', [AuthController::class, 'createSession']);
});


Route::prefix('users')->middleware('auth:sanctum')->group(function(){
    Route::get('/gifts', [UserController::class, 'searchGifts']);
    Route::get('/gift', [UserController::class, 'searchGift']);
});
