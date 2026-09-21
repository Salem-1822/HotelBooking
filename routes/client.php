<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\ReservationController;

// --- Client Registration (public/guest) ---
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

// --- Client Protected Routes (web guard) ---
Route::middleware(['auth:web'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\Client\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\Client\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\Client\ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Reservation flow
    Route::get('/reserve/{hotel}/{room}',  [ReservationController::class, 'create'])->name('reserve.create');
    Route::post('/reserve/{hotel}/{room}', [ReservationController::class, 'store'])->name('reserve.store');
    
    // Reservation Management
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::patch('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
    
    Route::get('/reservations/{reservation}/confirmation', [ReservationController::class, 'confirmation'])->name('reservations.confirmation');
});
