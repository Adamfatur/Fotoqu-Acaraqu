<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ErrorHandlingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $response = $next($request);
            
            // Log successful admin actions for audit
            if ($request->isMethod('POST') && $request->is('admin/*') && auth()->check()) {
                Log::info('Admin Action', [
                    'user_id' => auth()->id(),
                    'user_name' => auth()->user()->name,
                    'action' => $request->path(),
                    'method' => $request->method(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
            
            return $response;
            
        } catch (\Exception $e) {
            // Log the error with context
            Log::error('Request Error in Fotoku', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            // Re-throw the exception to be handled by the global handler
            throw $e;
        }
    }
}
