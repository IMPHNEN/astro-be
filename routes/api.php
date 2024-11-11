<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
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

            Route::put('/password', [AuthController::class, 'updatePassword']);
        });
    });


    Route::prefix('project')->group(function () {
        Route::post('/{slug}/apply', [ProjectController::class, 'apply']);
        Route::post('/{slug}/invest', [ProjectController::class, 'invest']);
        Route::post('/create', [ProjectController::class, 'create']);
        Route::put('/{slug}', [ProjectController::class, 'update']);
        Route::delete('/{slug}', [ProjectController::class, 'delete']);
    });


    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout/all', [AuthController::class, 'logoutAll']);
});

Route::prefix('project')->group(function () {
    Route::get('/all', [ProjectController::class, 'getAll']);
    Route::get('/view/{slug}', [ProjectController::class, 'getOne']);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
