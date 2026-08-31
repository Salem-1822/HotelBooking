<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SuperAdmin\LoginController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\HotelController;

// Client public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/hotels', [HotelController::class, 'index'])->name('hotels.index');
Route::get('/hotels/{hotel}', [HotelController::class, 'show'])->name('hotels.show');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
