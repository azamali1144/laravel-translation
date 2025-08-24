<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureApiToken
{
    public function handle(Request $request, Closure $next)
    {
        $auth = $request->header('Authorization');
        if (!$auth || !preg_match('/^Bearer\s+(.+)$/', $auth, $m)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $token = $m[1];

        // Simple token store (for test/dev); in production, use a database table.
        $validTokens = collect(config('app.api_tokens', [])); // e.g., ['token-abc' => true]
        if (!$validTokens->contains($token)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // You can set authenticated user info here if needed
        return $next($request);
    }
}
