<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use App\Services\AuthzClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Jumbojett\OpenIDConnectClient;

class ZitadelAuthController extends Controller
{
    /**
     * Redirect the user to Zitadel for authentication.
     */
    public function login()
    {
        $this->buildClient()->authenticate();
    }

    /**
     * Handle the OIDC callback from Zitadel.
     */
    public function callback(Request $request)
    {
        try {
            $oidc = $this->buildClient();
            $oidc->authenticate();

            $userInfo    = $oidc->requestUserInfo();
            $idToken     = $oidc->getIdTokenPayload();
            $accessToken = $oidc->getAccessToken();

            // Extract roles from the Zitadel project roles claim
            $projectId  = config('zitadel.project_id');
            $rolesClaim = "urn:zitadel:iam:org:project:{$projectId}:roles";

            $roles = [];
            if (isset($idToken->{$rolesClaim})) {
                $roles = array_keys((array) $idToken->{$rolesClaim});
            }

            // Build user data compatible with existing frontend (config.js UserManager)
            $user = [
                'sub'             => $userInfo->sub ?? $idToken->sub ?? null,
                'email'           => $userInfo->email ?? null,
                'name'            => $userInfo->name ?? $userInfo->preferred_username ?? null,
                'profile_picture' => $userInfo->picture ?? null,
                'roles'           => $roles,
            ];

            // Upsert local User record with zitadel_sub so the JWT guard can find them
            $sub   = $user['sub'];
            $email = $user['email'];
            $localUser = null;
            if ($sub && $email) {
                $localUser = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name'              => $user['name'] ?? $email,
                        'zitadel_sub'       => $sub,
                        'email_verified_at' => now(),
                        'password'          => bcrypt(str()->random(32)),
                    ]
                );

                // Auto-create organization for new users who don't have one yet
                $this->ensureOrganization($localUser, $sub);
            }

            // Store in Laravel session
            $request->session()->put('zitadel_user', $user);
            $request->session()->put('zitadel_access_token', $accessToken);

            // Store current org in session
            if ($localUser && $localUser->current_organization_id) {
                $request->session()->put('current_org_id', $localUser->current_organization_id);
            }
            $request->session()->put('zitadel_id_token', $oidc->getIdToken());
            $request->session()->put('zitadel_refresh_token', $oidc->getRefreshToken());

            // Store token expiry (Zitadel tokens typically expire in ~12h, use exp from ID token)
            $exp = $idToken->exp ?? (time() + 43200);
            $request->session()->put('zitadel_token_expires_at', $exp);

            // Redirect to bridge page that populates localStorage for existing JS
            return redirect()->route('zitadel.bridge');
        } catch (\Exception $e) {
            Log::error('Zitadel OIDC callback error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors([
                'zitadel' => __('Authentication failed. Please try again.'),
            ]);
        }
    }

    /**
     * Bridge page: injects Zitadel token + user into localStorage
     * so the existing frontend JS (config.js, ApiService) keeps working.
     */
    public function bridge(Request $request)
    {
        $user  = $request->session()->get('zitadel_user');
        $token = $request->session()->get('zitadel_access_token');

        if (! $user || ! $token) {
            return redirect()->route('zitadel.login');
        }

        $locale = $request->cookie('preferred_language') ?? 'en';
        $locale = in_array($locale, ['en', 'fr']) ? $locale : 'en';

        $userJson = json_encode([
            'id'              => $user['sub'],
            'name'            => $user['name'],
            'email'           => $user['email'],
            'profile_picture' => $user['profile_picture'],
            'last_active'     => now()->toISOString(),
        ]);

        return view('auth.bridge', [
            'token'     => $token,
            'userJson'  => $userJson,
            'locale'    => $locale,
        ]);
    }

    /**
     * Log the user out — clear localStorage, destroy session, redirect to Zitadel end_session.
     */
    public function logout(Request $request)
    {
        $idToken = $request->session()->get('zitadel_id_token');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $endSessionUrl = rtrim(config('zitadel.issuer'), '/') . '/oidc/v1/end_session';

        $params = ['post_logout_redirect_uri' => config('app.url')];
        if ($idToken) {
            $params['id_token_hint'] = $idToken;
        }

        // Return a page that clears localStorage before redirecting to Zitadel
        return view('auth.logout', [
            'redirectUrl' => $endSessionUrl . '?' . http_build_query($params),
        ]);
    }

    /**
     * Switch account — destroy session and redirect to Zitadel with prompt=select_account.
     */
    public function switchAccount(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $oidc = $this->buildClient();
        $oidc->addAuthParam(['prompt' => 'select_account']);
        $oidc->authenticate();
    }

    /**
     * Refresh the access token using the stored refresh token.
     * Returns JSON with the new token (called from JS when a 401 is detected).
     */
    public function refresh(Request $request)
    {
        $refreshToken = $request->session()->get('zitadel_refresh_token');

        if (! $refreshToken) {
            return response()->json(['error' => 'no_refresh_token'], 401);
        }

        try {
            $oidc = $this->buildClient();
            $oidc->refreshToken($refreshToken);

            $accessToken = $oidc->getAccessToken();
            $newRefresh  = $oidc->getRefreshToken();
            $idToken     = $oidc->getIdToken();

            $request->session()->put('zitadel_access_token', $accessToken);
            $request->session()->put('zitadel_id_token', $idToken);
            if ($newRefresh) {
                $request->session()->put('zitadel_refresh_token', $newRefresh);
            }

            // Update expiry (parse new id_token for exp)
            $payload = $oidc->getIdTokenPayload();
            $exp = $payload->exp ?? (time() + 43200);
            $request->session()->put('zitadel_token_expires_at', $exp);

            return response()->json(['token' => $accessToken]);
        } catch (\Exception $e) {
            Log::warning('Token refresh failed: ' . $e->getMessage());
            return response()->json(['error' => 'refresh_failed'], 401);
        }
    }

    /**
     * Ensure the user has an organization. Creates one and assigns owner role if needed.
     */
    private function ensureOrganization(User $user, string $sub): void
    {
        if ($user->current_organization_id) {
            return;
        }

        // Check if user already owns an org (e.g., created via API)
        $org = Organization::where('owner_id', $user->id)->first();

        if (! $org) {
            $orgName = $user->name ?: 'Mon organisation';
            $org = Organization::create([
                'name'     => $orgName,
                'owner_id' => $user->id,
            ]);

            // Assign owner role on the new org in the authz service
            try {
                $authz = app(AuthzClient::class);
                $ownerRoleId = 1; // owner role ID from rbac.php
                $authz->assignRole($sub, $ownerRoleId, config('rbac.targets.org'), (string) $org->id);
            } catch (\Exception $e) {
                Log::warning('Failed to assign owner role on new org', [
                    'user_id' => $user->id,
                    'org_id'  => $org->id,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        // Set as current org
        $user->update(['current_organization_id' => $org->id]);
    }

    /**
     * Build a configured OpenIDConnectClient instance.
     */
    private function buildClient(): OpenIDConnectClient
    {
        $oidc = new OpenIDConnectClient(
            config('zitadel.issuer'),
            config('zitadel.client_id'),
            config('zitadel.client_secret')
        );

        $oidc->setRedirectURL(config('zitadel.redirect_uri'));
        $oidc->addScope(['openid', 'profile', 'email', 'offline_access', 'urn:zitadel:iam:org:projects:roles']);

        return $oidc;
    }
}
