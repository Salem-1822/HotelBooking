<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HotelController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\LoginController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->name('admin.')->group(function () {
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
        Route::get('cities/export', [CityController::class, 'exportPDF'])->name('cities.export');
        Route::resource('cities', CityController::class);
        
        // Admins (Only Super Admin)
        Route::middleware(['role:super_admin'])->group(function () {
            Route::get('admins/export', [AdminUserController::class, 'exportPDF'])->name('admins.export');
            Route::resource('admins', AdminUserController::class);
            
            // System Configuration
            Route::get('/settings', [\App\Http\Controllers\Admin\SystemConfigController::class, 'index'])->name('settings');
            Route::post('/settings', [\App\Http\Controllers\Admin\SystemConfigController::class, 'update'])->name('settings.update');

            // Profile
            Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile');
            Route::post('/profile/update', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
        });
        
        // Reservations
        Route::get('reservations/export', [ReservationController::class, 'exportPDF'])->name('reservations.export');
        Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
        
    });
});
