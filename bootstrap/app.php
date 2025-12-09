<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'photobox/*'
        ]);
        
        // Register custom middleware aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'error.handling' => \App\Http\Middleware\ErrorHandlingMiddleware::class,
            'photobox.token' => \App\Http\Middleware\EnsurePhotoboxTokenIsValid::class,
        ]);
        
        // Apply error handling middleware globally
        $middleware->web(append: [
            \App\Http\Middleware\ErrorHandlingMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle specific exceptions for better UX
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'API endpoint tidak ditemukan',
                    'status_code' => 404,
                ], 404);
            }
            
            return response()->view('errors.404', ['exception' => $e], 404);
        });
        
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda perlu login untuk mengakses resource ini',
                    'status_code' => 401,
                ], 401);
            }
            
            return redirect()->guest(route('login'));
        });
        
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak',
                    'status_code' => 403,
                ], 403);
            }
            
            return response()->view('errors.403', ['exception' => $e], 403);
        });
        
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi telah berakhir, silakan refresh halaman',
                    'status_code' => 419,
                ], 419);
            }
            
            return response()->view('errors.419', ['exception' => $e], 419);
        });
    })->create();
