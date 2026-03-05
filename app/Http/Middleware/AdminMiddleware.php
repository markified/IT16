<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AdminMiddleware - Restricts access to SUPERADMIN role ONLY
 * 
 * This middleware ensures that ONLY users with the 'superadmin' role can access
 * protected routes. No other role (including 'admin' if it exists) should have access.
 * 
 * Note: Despite the class name 'AdminMiddleware', this strictly checks for 'superadmin' role.
 */
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     * Only allows users with 'superadmin' role to proceed.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Strict check: ONLY 'superadmin' role is allowed
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Access denied. Only superadmin users can access this page.');
        }

        return $next($request);
    }
}
