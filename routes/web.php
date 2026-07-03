<?php

use Illuminate\Support\Facades\Route;

// Client module routes will be added here

Route::get('/', function () {
    // For now, redirect to super admin login. Later this will be the client home page.
    return redirect()->route('super_admin.login');
});
