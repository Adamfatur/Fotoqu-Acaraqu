<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Log critical errors for Fotoku
            if ($e instanceof QueryException) {
                \Log::error('Database Error in Fotoku', [
                    'message' => $e->getMessage(),
                    'sql' => $e->getSql() ?? 'N/A',
                    'bindings' => $e->getBindings() ?? [],
                    'url' => request()->fullUrl(),
                    'user_id' => auth()->id(),
                ]);
            }
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e): Response
    {
        // Handle AJAX requests with JSON responses
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $e);
        }

        // Handle specific exceptions with custom error pages
        if ($e instanceof NotFoundHttpException) {
            return response()->view('errors.404', [
                'exception' => $e
            ], 404);
        }

        if ($e instanceof AccessDeniedHttpException) {
            return response()->view('errors.403', [
                'exception' => $e
            ], 403);
        }

        if ($e instanceof TokenMismatchException) {
            return response()->view('errors.419', [
                'exception' => $e
            ], 419);
        }

        // Handle HTTP exceptions
        if ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();
            
            // Check if we have a custom error view for this status code
            if (view()->exists("errors.{$statusCode}")) {
                return response()->view("errors.{$statusCode}", [
                    'exception' => $e
                ], $statusCode);
            }
        }

        // Handle Database exceptions
        if ($e instanceof QueryException) {
            if (config('app.debug')) {
                // In development, show detailed error
                return parent::render($request, $e);
            } else {
                // In production, show friendly error page
                return response()->view('errors.500', [
                    'exception' => $e
                ], 500);
            }
        }

        // Handle authentication exceptions
        if ($e instanceof AuthenticationException) {
            return redirect()->guest(route('login'));
        }

        // Default error handling
        if (!config('app.debug')) {
            // In production, always show friendly error page for unhandled exceptions
            return response()->view('errors.500', [
                'exception' => $e
            ], 500);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle API/AJAX exceptions with JSON responses
     */
    protected function handleApiException(Request $request, Throwable $e)
    {
        $statusCode = 500;
        $message = 'Terjadi kesalahan pada server';

        if ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();
            $message = $this->getHttpExceptionMessage($statusCode);
        } elseif ($e instanceof NotFoundHttpException) {
            $statusCode = 404;
            $message = 'Resource tidak ditemukan';
        } elseif ($e instanceof AccessDeniedHttpException) {
            $statusCode = 403;
            $message = 'Akses ditolak';
        } elseif ($e instanceof TokenMismatchException) {
            $statusCode = 419;
            $message = 'Sesi telah berakhir, silakan refresh halaman';
        } elseif ($e instanceof AuthenticationException) {
            $statusCode = 401;
            $message = 'Anda perlu login untuk mengakses resource ini';
        } elseif ($e instanceof QueryException) {
            $statusCode = 500;
            $message = 'Terjadi kesalahan database';
        }

        $response = [
            'success' => false,
            'message' => $message,
            'status_code' => $statusCode,
        ];

        // Add debug info in development
        if (config('app.debug')) {
            $response['debug'] = [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ];
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Get user-friendly message for HTTP status codes
     */
    protected function getHttpExceptionMessage(int $statusCode): string
    {
        return match($statusCode) {
            400 => 'Permintaan tidak valid',
            401 => 'Anda perlu login',
            403 => 'Akses ditolak',
            404 => 'Halaman tidak ditemukan',
            405 => 'Method tidak diizinkan',
            419 => 'Sesi telah berakhir',
            422 => 'Data yang dikirim tidak valid',
            429 => 'Terlalu banyak permintaan',
            500 => 'Kesalahan server internal',
            502 => 'Bad gateway',
            503 => 'Layanan tidak tersedia',
            504 => 'Gateway timeout',
            default => 'Terjadi kesalahan',
        };
    }

    /**
     * Convert an authentication exception into a response.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda perlu login untuk mengakses resource ini',
                'status_code' => 401,
            ], 401);
        }

        return redirect()->guest($exception->redirectTo() ?? route('login'));
    }
}
