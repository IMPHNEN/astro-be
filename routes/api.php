<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('profile')->group(function () {
        Route::get('/', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);
        
        Route::prefix('update')->group(function () {
            Route::post('/', [AuthController::class, 'updateProfile']);
            Route::post('/avatar', [AuthController::class, 'updateAvatar']);
            Route::post('/cover', [AuthController::class, 'updateCover']);
            Route::post('/cv', [AuthController::class, 'updateCV']);
            Route::post('/password', [AuthController::class, 'updatePassword']);
        });
    });
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
