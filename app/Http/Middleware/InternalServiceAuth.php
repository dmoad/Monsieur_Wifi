<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InternalServiceAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        $expected = config('services.internal.key');

        if (! $token || ! $expected || ! hash_equals($expected, $token)) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        return $next($request);
    }
}
