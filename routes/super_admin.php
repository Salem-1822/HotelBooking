<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\HotelController;
use App\Http\Controllers\SuperAdmin\CityController;
use App\Http\Controllers\SuperAdmin\ReservationController;
use App\Http\Controllers\SuperAdmin\LoginController;
use App\Http\Controllers\SuperAdmin\SystemConfigController;
use App\Http\Controllers\SuperAdmin\ProfileController;
use App\Http\Controllers\SuperAdmin\AdminUserController;

// Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Hotels
    Route::get('hotels/export', [HotelController::class, 'exportPDF'])->name('hotels.export');
    Route::resource('hotels', HotelController::class);
    
    // Cities
    Route::resource('cities', CityController::class)->except(['create', 'edit', 'show']);
    
    // Admins (Only Super Admin)
    Route::middleware(['role:super_admin'])->group(function () {
        
        // Admins Management
        Route::resource('admins', AdminUserController::class)->except(['create', 'edit', 'show']);

        // System Configuration
        Route::get('/settings', [SystemConfigController::class, 'index'])->name('settings');
        Route::post('/settings', [SystemConfigController::class, 'update'])->name('settings.update');

        // Profile
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    });
    
    // Reservations
    Route::get('reservations/export', [ReservationController::class, 'exportPDF'])->name('reservations.export');
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    
});
