<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SuperAdmin\LoginController;

// Client module routes will be added here

Route::get('/', function () {
    // For now, redirect to shared login. Later this will be the client home page.
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
