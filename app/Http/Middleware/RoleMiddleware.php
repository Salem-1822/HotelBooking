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
            if (in_array('super_admin', $roles, true)) {
                return redirect()->route('super_admin.login');
            }
            if (in_array('admin', $roles, true)) {
                return redirect()->route('admin.login');
            }
            return redirect()->route('login');
        }

        // 3. Authenticated admin check
        $admin = Auth::guard('admin')->user();

        // 3a. Check if the regular administrator account has been blocked
        if ($admin->role === 'admin' && ($admin->is_blocked || $admin->status === 'blocked')) {
            $adminId = $admin->id;
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $request->session()->put('blocked_admin_id', $adminId);

            if ($request->expectsJson()) {
                abort(403, 'Your administrator account has been suspended.');
            }

            return redirect()->route('admin.blocked');
        }

        // 3b. Authenticated admin does not possess the required role
        if (! in_array($admin->role, $roles, true)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
