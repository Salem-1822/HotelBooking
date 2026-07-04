<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\DashboardController;

Route::middleware(['auth:admin', 'role:client'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
