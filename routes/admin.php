<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\HotelProfileController;
use App\Http\Controllers\Admin\PasswordResetController;

// --- Admin Authentication Routes (public/guest) ---
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/blocked', [LoginController::class, 'showBlocked'])->name('blocked');

// --- Admin Forgot Password / Reset Password (public/guest) ---
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])
    ->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
    ->middleware('throttle:5,1')
    ->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
    ->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
    ->name('password.update');

Route::middleware(['auth:admin', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('rooms', RoomController::class)->except(['create', 'edit', 'show']);
    Route::resource('reservations', ReservationController::class)->except(['create', 'edit', 'show']);

    // Customers Management
    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::patch('customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    // Reports Management
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'exportPDF'])->name('reports.export');

    // Hotel Profile
    Route::get('hotel-profile', [HotelProfileController::class, 'show'])->name('hotel-profile');
    Route::put('hotel-profile', [HotelProfileController::class, 'update'])->name('hotel-profile.update');

    // Admin Profile
    Route::get('profile', [App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile');
    Route::put('profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('profile/photo', [App\Http\Controllers\Admin\ProfileController::class, 'removePhoto'])->name('profile.photo.destroy');
});
