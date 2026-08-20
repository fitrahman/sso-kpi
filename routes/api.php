<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public routes with Rate Limiting (10 requests per minute)
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    // Protected routes
    Route::middleware('auth:api')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // Role Synchronization API
        Route::get('/client-roles', [AuthController::class, 'getClientRoles']);

        // User routes
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
    });
});

// Alias for /api/user without v1 prefix
Route::middleware('auth:api')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
});
