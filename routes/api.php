<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'profile']);
        
        Route::prefix('update')->group(function () {
            Route::put('/', [ProfileController::class, 'updateProfile']);
            Route::post('/avatar', [ProfileController::class, 'updateAvatar']);
            Route::post('/background', [ProfileController::class, 'updateBackground']);
            Route::post('/cv', [ProfileController::class, 'updateCV']);
            Route::put('/password', [ProfileController::class, 'updatePassword']);
        });
    });
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');