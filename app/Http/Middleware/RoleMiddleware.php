<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. If a client user (web guard) attempts to access an admin-scoped route, deny immediately with 403
        if (Auth::guard('web')->check() && ! Auth::guard('admin')->check()) {
            abort(403, 'Unauthorized access.');
        }

        // 2. Unauthenticated check for admin guard
        if (! Auth::guard('admin')->check()) {
            if ($request->expectsJson()) {
                abort(401, 'Unauthenticated.');
            }
            return redirect()->route('login');
        }

        // 3. Authenticated admin does not possess the required role
        $admin = Auth::guard('admin')->user();
        if (! in_array($admin->role, $roles, true)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
