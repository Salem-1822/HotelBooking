<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\HotelController;

// Client public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/hotels', [HotelController::class, 'index'])->name('hotels.index');
Route::get('/hotels/{hotel}', [HotelController::class, 'show'])->name('hotels.show');

Route::view('/about', 'client.about')->name('about');

Route::get('/contact', [\App\Http\Controllers\Client\ContactController::class, 'index'])->name('contact');
Route::post('/contact', [\App\Http\Controllers\Client\ContactController::class, 'store'])->middleware('throttle:10,1')->name('contact.store');

// Client Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/**
 * Public Storage File Serving Fallback Route
 * Serves files directly if the web server did not intercept them statically.
 */
Route::get('/storage/{path}', function (string $path) {
    // Sanitize path against directory traversal
    $path = str_replace(['..', "\0"], '', $path);
    $path = ltrim($path, '/');

    $candidates = [
        public_path('storage/' . $path),
        storage_path('app/public/' . $path),
    ];

    foreach ($candidates as $filePath) {
        if (is_file($filePath) && file_exists($filePath)) {
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $mimeType = match ($ext) {
                'jpg', 'jpeg' => 'image/jpeg',
                'png'         => 'image/png',
                'webp'        => 'image/webp',
                'gif'         => 'image/gif',
                'svg'         => 'image/svg+xml',
                default       => mime_content_type($filePath) ?: 'application/octet-stream',
            };

            return response()->file($filePath, [
                'Content-Type'  => $mimeType,
                'Cache-Control' => 'public, max-age=31536000, immutable',
            ]);
        }
    }

    abort(404);
})->where('path', '.*')->name('storage.fallback');
