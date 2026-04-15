<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HotelController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\ReservationController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Hotels
    Route::get('hotels/export', [HotelController::class, 'exportPDF'])->name('hotels.export');
    Route::resource('hotels', HotelController::class);
    
    // Cities
    Route::get('cities/export', [CityController::class, 'exportPDF'])->name('cities.export');
    Route::resource('cities', CityController::class);
    
    // Admins
    Route::get('admins/export', [AdminUserController::class, 'exportPDF'])->name('admins.export');
    Route::resource('admins', AdminUserController::class);
    
    // Reservations
    Route::get('reservations/export', [ReservationController::class, 'exportPDF'])->name('reservations.export');
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
    Route::get('/exports', [DashboardController::class, 'exports'])->name('exports');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
});
