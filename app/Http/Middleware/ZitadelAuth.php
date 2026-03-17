<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Jumbojett\OpenIDConnectClient;
use Symfony\Component\HttpFoundation\Response;

class ZitadelAuth
{
    /**
     * Handle an incoming request.
     *
     * Checks that a Zitadel user session exists and optionally
     * verifies the user has the required role.
     * Proactively refreshes the access token if it expires within 5 minutes.
     *
     * Usage in routes:
     *   ->middleware('zitadel')          // requires 'member' role (default)
     *   ->middleware('zitadel:pending')  // requires 'pending' role
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->session()->get('zitadel_user');

        // No session — redirect to login
        if (! $user) {
            return redirect()->route('zitadel.login');
        }

        // Proactively refresh token if expiring within 5 minutes
        $expiresAt    = $request->session()->get('zitadel_token_expires_at', 0);
        $refreshToken = $request->session()->get('zitadel_refresh_token');

        if ($refreshToken && $expiresAt && $expiresAt - time() < 300) {
            try {
                $oidc = new OpenIDConnectClient(
                    config('zitadel.issuer'),
                    config('zitadel.client_id'),
                    config('zitadel.client_secret')
                );
                $oidc->addScope(['openid', 'profile', 'email', 'offline_access', 'urn:zitadel:iam:org:projects:roles']);
                $oidc->refreshToken($refreshToken);

                $request->session()->put('zitadel_access_token', $oidc->getAccessToken());
                $request->session()->put('zitadel_id_token', $oidc->getIdToken());
                if ($oidc->getRefreshToken()) {
                    $request->session()->put('zitadel_refresh_token', $oidc->getRefreshToken());
                }
                $payload = $oidc->getIdTokenPayload();
                $request->session()->put('zitadel_token_expires_at', $payload->exp ?? (time() + 43200));
            } catch (\Exception $e) {
                Log::warning('ZitadelAuth: proactive token refresh failed', ['err' => $e->getMessage()]);
            }
        }

        // Roles are now handled by the authz service, not Zitadel project roles.
        // This middleware only verifies the user has a valid Zitadel session.
        return $next($request);
    }
}
