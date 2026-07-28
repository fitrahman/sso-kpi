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
    Route::post('/sso/request-access', [DashboardController::class, 'requestAccess'])->name('app.requestAccess');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // User profile update request
    Route::post('/profile/update-request', [DashboardController::class, 'updateProfileRequest'])->name('profile.updateRequest');

    // Admin only routes
    Route::middleware('admin')->group(function () {
        Route::get('/admin/users', [DashboardController::class, 'users'])->name('admin.users');
        Route::get('/admin/users/{id}/edit', [DashboardController::class, 'editUser'])->name('admin.users.edit');
        Route::put('/admin/users/{id}', [DashboardController::class, 'updateUser'])->name('admin.users.update');
        
        // Approval Routes
        Route::post('/admin/users/{id}/approve', [DashboardController::class, 'approveUser'])->name('admin.users.approve');
        Route::delete('/admin/users/{id}/reject', [DashboardController::class, 'rejectUser'])->name('admin.users.reject');

        // App Access Requests
        Route::get('/admin/access-requests', [DashboardController::class, 'accessRequests'])->name('admin.accessRequests');
        Route::post('/admin/access-requests/{userId}/{clientId}/approve', [DashboardController::class, 'approveAppAccess'])->name('admin.accessRequests.approve');
        Route::post('/admin/access-requests/{userId}/{clientId}/undo-reject', [DashboardController::class, 'undoRejectAppAccess'])->name('admin.accessRequests.undoReject');
        Route::delete('/admin/access-requests/{userId}/{clientId}/reject', [DashboardController::class, 'rejectAppAccess'])->name('admin.accessRequests.reject');

        // Admin Profile Update Requests
        Route::get('/admin/profile-requests', [DashboardController::class, 'profileRequests'])->name('admin.profileRequests');
        Route::post('/admin/profile-requests/{id}/approve', [DashboardController::class, 'approveProfileRequest'])->name('admin.profileRequests.approve');
        Route::post('/admin/profile-requests/{id}/reject', [DashboardController::class, 'rejectProfileRequest'])->name('admin.profileRequests.reject');
    });
});
