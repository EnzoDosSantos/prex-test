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
    Route::post('/login', [AuthController::class, 'createSession'])->middleware('log:SESSION');
});


Route::prefix('external')->middleware('auth:sanctum')->group(function(){
    Route::get('/gifts', [UserController::class, 'searchExternalGifts'])->middleware('log:EXTERNAL_SEARCH_GIFTS');
    Route::get('/gift', [UserController::class, 'searchExternalGift'])->middleware('log:EXTERNAL_SEARCH_GIFT');
});

Route::prefix('internal')->middleware('auth:sanctum')->group(function(){
    Route::get('/gifts', [UserController::class, 'searchInternalGifts'])->middleware('log:INTERNAL_SEARCH_GIFTS');
    Route::get('/gift/{id}', [UserController::class, 'searchInternalGift'])->middleware('log:INTERNAL_SEARCH_GIFT');
    Route::post('/gift', [UserController::class, 'updateFavouriteGift'])->middleware('log:INTERNAL_FAVOURITE_GIFT');
});
