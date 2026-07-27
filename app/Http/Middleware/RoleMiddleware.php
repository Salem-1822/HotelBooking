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
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! Auth::guard('admin')->check()) {
            return redirect()->route('login');
        }

        if (Auth::guard('admin')->user()->role !== $role) {
            $userRole = Auth::guard('admin')->user()->role;
            if ($userRole === 'super_admin') {
                return redirect()->route('super_admin.dashboard');
            } elseif ($userRole === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($userRole === 'client') {
                return redirect()->route('client.dashboard');
            }
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
