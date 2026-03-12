<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust all proxies to get real client IP address
        $middleware->trustProxies(at: '*');
        
        // Append session security middleware to web group
        $middleware->web(append: [
            \App\Http\Middleware\SessionSecurityMiddleware::class,
        ]);
        
        $middleware->alias([
            'superadmin' => \App\Http\Middleware\AdminMiddleware::class,
            'admin' => \App\Http\Middleware\AdminViewMiddleware::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'inventory' => \App\Http\Middleware\InventoryMiddleware::class,
            'security' => \App\Http\Middleware\SecurityMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle 404 - Model Not Found
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found.',
                ], 404);
            }

            return response()->view('errors.404', [
                'message' => 'The requested resource was not found.',
            ], 404);
        });

        // Handle 404 - Route Not Found
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Page not found.',
                ], 404);
            }

            return response()->view('errors.404', [
                'message' => 'Page not found.',
            ], 404);
        });

        // Handle 403 - Access Denied
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Access denied.',
                ], 403);
            }

            return response()->view('errors.403', [
                'message' => $e->getMessage() ?: 'You do not have permission to access this resource.',
            ], 403);
        });

        // Handle Authentication Exceptions
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login.',
                ], 401);
            }

            return redirect()->route('login')->with('error', 'Please login to continue.');
        });

        // Handle Database Query Exceptions
        $exceptions->render(function (QueryException $e, Request $request) {
            // Log the error for debugging
            \Illuminate\Support\Facades\Log::error('Database error: '.$e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'A database error occurred. Please try again.',
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'A database error occurred. Please try again or contact support.');
        });

        // Handle generic HTTP exceptions
        $exceptions->render(function (HttpException $e, Request $request) {
            $statusCode = $e->getStatusCode();
            $message = $e->getMessage() ?: 'An error occurred.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], $statusCode);
            }

            // Use custom error view if exists, otherwise use default
            $view = view()->exists("errors.{$statusCode}")
                ? "errors.{$statusCode}"
                : 'errors.generic';

            return response()->view($view, [
                'message' => $message,
                'code' => $statusCode,
            ], $statusCode);
        });
    })->create();
