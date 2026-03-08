<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AdminViewMiddleware - Allows both superadmin and admin roles
 *
 * This middleware allows users with 'superadmin' or 'admin' role to access
 * specific administrative features like viewing users, audit logs, and reports.
 */
class AdminViewMiddleware
{
    /**
     * Handle an incoming request.
     * Allows users with 'superadmin' or 'admin' role to proceed.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        // Allow both 'superadmin' and 'admin' roles
        $allowedRoles = ['superadmin', 'admin'];

        if (! in_array(auth()->user()->role, $allowedRoles)) {
            abort(403, 'Access denied. Only administrators can access this page.');
        }

        return $next($request);
    }
}
