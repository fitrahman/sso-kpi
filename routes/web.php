<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use Illuminate\Support\Facades\Route;

// Public routes with rate limiting for sensitive entry points
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::post('/forgot-password', [\App\Http\Controllers\Web\PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::post('/reset-password', [\App\Http\Controllers\Web\PasswordResetController::class, 'reset'])->name('password.update');
});

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/login', function () {
    return redirect('/');
});
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::view('/register/pending', 'auth.register-pending')->name('register.pending');

// Password Reset Routes
Route::get('/forgot-password', [\App\Http\Controllers\Web\PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::get('/reset-password/{token}', [\App\Http\Controllers\Web\PasswordResetController::class, 'showResetForm'])->name('password.reset');

// SSO Logout Route (Public, so it can always redirect back even if session expired)
Route::get('/sso-logout', [AuthController::class, 'ssoLogout'])->name('sso.logout');

// Protected routes
Route::middleware('auth:web')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/sso/gateway', [DashboardController::class, 'appGateway'])->name('app.gateway');
    Route::get('/sso/maintenance', function () {
        return view('auth.app-maintenance', [
            'appName' => request('appName', 'Aplikasi'),
            'message' => request('message'),
        ]);
    })->name('app.maintenance');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin only routes
    Route::middleware('admin')->group(function () {
        Route::get('/admin/stats', [DashboardController::class, 'stats'])->name('admin.stats');
        Route::get('/admin/users', [DashboardController::class, 'users'])->name('admin.users');
        Route::get('/admin/users/{id}/edit', [DashboardController::class, 'editUser'])->name('admin.users.edit');
        Route::put('/admin/users/{id}', [DashboardController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/admin/users/{id}', [DashboardController::class, 'deleteUser'])->name('admin.users.delete');

        // Approval Routes
        Route::post('/admin/users/{id}/approve', [DashboardController::class, 'approveUser'])->name('admin.users.approve');
        Route::delete('/admin/users/{id}/reject', [DashboardController::class, 'rejectUser'])->name('admin.users.reject');

        // Application Management Routes
        Route::get('/admin/applications', [DashboardController::class, 'clients'])->name('admin.clients');
        Route::post('/admin/applications', [DashboardController::class, 'storeClient'])->name('admin.clients.store');
        Route::get('/admin/applications/{id}/users', [DashboardController::class, 'clientUsers'])->name('admin.clients.users');
        Route::put('/admin/applications/{id}/users/{userId}', [DashboardController::class, 'updateClientUser'])->name('admin.clients.users.update');
        Route::put('/admin/applications/{id}', [DashboardController::class, 'updateClient'])->name('admin.clients.update');
        Route::delete('/admin/applications/{id}', [DashboardController::class, 'deleteClient'])->name('admin.clients.delete');
        Route::post('/admin/applications/{id}/toggle-maintenance', [DashboardController::class, 'toggleMaintenance'])->name('admin.clients.maintenance');
        Route::post('/admin/applications/{id}/toggle-visibility', [DashboardController::class, 'toggleVisibility'])->name('admin.clients.visibility');
        Route::delete('/admin/applications/{id}/logo', [DashboardController::class, 'deleteClientLogo'])->name('admin.clients.logo.delete');
    });
});
