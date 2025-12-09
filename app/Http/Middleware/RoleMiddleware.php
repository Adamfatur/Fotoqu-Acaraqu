<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user is banned
        if ($user->isBanned()) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Akun Anda telah diblokir. Silakan hubungi administrator.');
        }

        // Check if user is active
        if (!$user->isActive()) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Akun Anda tidak aktif. Silakan hubungi administrator.');
        }

        // Check if user has required role
        if (!in_array($user->role, $roles)) {
            abort(403, 'Anda tidak memiliki akses untuk halaman ini.');
        }

        return $next($request);
    }
}
