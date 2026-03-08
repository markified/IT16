<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InventoryMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $allowedRoles = ['superadmin', 'inventory'];

        if (! in_array(auth()->user()->role, $allowedRoles)) {
            abort(403, 'Access denied. Only inventory managers and administrators can access this page.');
        }

        return $next($request);
    }
}
