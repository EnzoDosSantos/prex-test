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
    Route::get('/gifs', [UserController::class, 'searchExternalGifs'])->middleware('log:EXTERNAL_SEARCH_GIFS');
    Route::get('/gif', [UserController::class, 'searchExternalGif'])->middleware('log:EXTERNAL_SEARCH_GIF');
});

Route::prefix('internal')->middleware('auth:sanctum')->group(function(){
    Route::get('/gifs', [UserController::class, 'searchInternalGifs'])->middleware('log:INTERNAL_SEARCH_GIFS');
    Route::get('/gif/{id}', [UserController::class, 'searchInternalGif'])->middleware('log:INTERNAL_SEARCH_GIF');
    Route::post('/gif', [UserController::class, 'updateFavouriteGif'])->middleware('log:INTERNAL_FAVOURITE_GIF');
});
