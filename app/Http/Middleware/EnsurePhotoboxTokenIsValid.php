<?php

namespace App\Http\Middleware;

use App\Models\Photobox;
use App\Models\PhotoboxAccessToken;
use Closure;
use Illuminate\Http\Request;

class EnsurePhotoboxTokenIsValid
{
    /**
     * Handle an incoming request.
     * Accept token from query (?token=) or header (X-Photobox-Token).
     * Validate against DB with expiry and photobox binding.
     */
    public function handle(Request $request, Closure $next)
    {
        // Resolve photobox model from route if present
        /** @var Photobox|null $photobox */
        $photobox = $request->route('photobox');

        $tokenValue = $request->query('token') ?: $request->header('X-Photobox-Token');
        if (!$tokenValue) {
            return response()->json(['error' => 'Missing token'], 403);
        }

        $token = PhotoboxAccessToken::where('token', $tokenValue)
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$token) {
            return response()->json(['error' => 'Invalid or expired token'], 403);
        }

        if ($photobox && $token->photobox_id !== $photobox->id) {
            return response()->json(['error' => 'Token not valid for this photobox'], 403);
        }

        // Attach token to request for controllers if needed
        $request->attributes->set('photobox_access_token', $token);

        return $next($request);
    }
}
