<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['web'])
                ->prefix('super-admin')
                ->name('super_admin.')
                ->group(base_path('routes/super_admin.php'));

            Route::middleware(['web'])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));

            Route::middleware(['web'])
                ->prefix('client')
                ->name('client.')
                ->group(base_path('routes/client.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectTo(
            guests: function (\Illuminate\Http\Request $request) {
                if (\Illuminate\Support\Facades\Auth::guard('web')->check() && ($request->is('admin*') || $request->is('super-admin*'))) {
                    abort(403, 'Unauthorized access.');
                }
                if (\Illuminate\Support\Facades\Auth::guard('admin')->check() && $request->is('client*')) {
                    abort(403, 'Unauthorized access.');
                }
                if ($request->is('super-admin*')) {
                    return $request->expectsJson() ? null : route('super_admin.login');
                }
                if ($request->is('admin*')) {
                    return $request->expectsJson() ? null : route('admin.login');
                }
                return $request->expectsJson() ? null : route('login');
            },
            users: function (\Illuminate\Http\Request $request) {
                if (\Illuminate\Support\Facades\Auth::guard('admin')->check()) {
                    $admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
                    if ($admin->role === 'admin' && ($admin->is_blocked || $admin->status === 'blocked')) {
                        \Illuminate\Support\Facades\Auth::guard('admin')->logout();
                        session(['blocked_admin_id' => $admin->id]);
                        return route('admin.blocked');
                    }
                    return $admin->role === 'super_admin'
                        ? route('super_admin.dashboard')
                        : route('admin.dashboard');
                }
                if (\Illuminate\Support\Facades\Auth::guard('web')->check()) {
                    return route('client.dashboard');
                }
                return route('home');
            }
        );
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
