<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use Illuminate\Support\Facades\Route;

// Public routes
// Route::get('/', function () {
//     return view('welcome');
// })->name('home');

// Public routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/login', function () {
    return redirect('/');
});
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::view('/register/pending', 'auth.register-pending')->name('register.pending');

    // Password Reset Routes
    Route::get('/forgot-password', [\App\Http\Controllers\Web\PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Web\PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\Web\PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\Web\PasswordResetController::class, 'reset'])->name('password.update');

    // SSO Logout Route (Public, so it can always redirect back even if session expired)
    Route::get('/sso-logout', [AuthController::class, 'ssoLogout'])->name('sso.logout');

    // Protected routes
Route::middleware('auth:web')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/sso/gateway', [DashboardController::class, 'appGateway'])->name('app.gateway');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    


    // Admin only routes
    Route::middleware('admin')->group(function () {
        Route::get('/admin/users', [DashboardController::class, 'users'])->name('admin.users');
        Route::get('/admin/users/{id}/edit', [DashboardController::class, 'editUser'])->name('admin.users.edit');
        Route::put('/admin/users/{id}', [DashboardController::class, 'updateUser'])->name('admin.users.update');
        Route::post('/admin/users/{id}/deactivate', [DashboardController::class, 'deactivateUser'])->name('admin.users.deactivate');
        Route::post('/admin/users/{id}/activate', [DashboardController::class, 'activateUser'])->name('admin.users.activate');
        Route::delete('/admin/users/{id}', [DashboardController::class, 'deleteUser'])->name('admin.users.delete');
        
        // Approval Routes
        Route::post('/admin/users/{id}/approve', [DashboardController::class, 'approveUser'])->name('admin.users.approve');
        Route::delete('/admin/users/{id}/reject', [DashboardController::class, 'rejectUser'])->name('admin.users.reject');




    });
});
