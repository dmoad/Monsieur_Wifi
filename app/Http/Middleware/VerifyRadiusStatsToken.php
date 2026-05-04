<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyRadiusStatsToken
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('Radius guest-session-stats request', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $secret = (string) (config('services.radius.stats_secret') ?? '');

        if ($secret === '') {
            Log::warning('Radius guest-session-stats rejected: RADIUS_STATS_SECRET is not set');

            abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
        }

        $token = $request->bearerToken()
            ?? $request->header('X-Api-Token');

        if ($token === null || $token === '' || ! hash_equals($secret, (string) $token)) {
            Log::warning('Radius guest-session-stats rejected: invalid or missing token', [
                'ip' => $request->ip(),
            ]);

            abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
        }

        return $next($request);
    }
}
