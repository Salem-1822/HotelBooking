<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\HotelProfileController;

Route::middleware(['auth:admin', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('rooms', RoomController::class)->except(['create', 'edit', 'show']);
    Route::resource('reservations', ReservationController::class);

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
