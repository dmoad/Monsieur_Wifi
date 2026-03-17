<?php

namespace App\Auth;

use App\Models\User;
use App\Services\AuthzService;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ZitadelJwtGuard implements Guard
{
    protected Request $request;
    protected ?Authenticatable $user = null;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function guest(): bool
    {
        return ! $this->check();
    }

    public function id()
    {
        return $this->user()?->getAuthIdentifier();
    }

    public function user()
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $token = $this->request->bearerToken();
        if (! $token) {
            return null;
        }

        try {
            $keys = $this->getJwks();
            $decoded = JWT::decode($token, $keys);

            // Verify issuer
            $expectedIssuer = rtrim(config('zitadel.issuer'), '/');
            $tokenIssuer = rtrim($decoded->iss ?? '', '/');
            if ($tokenIssuer !== $expectedIssuer) {
                Log::warning('Zitadel JWT issuer mismatch', [
                    'expected' => $expectedIssuer,
                    'got' => $tokenIssuer,
                ]);
                return null;
            }

            // Extract roles from Zitadel project claim
            $projectId = config('zitadel.project_id');
            $rolesClaim = "urn:zitadel:iam:org:project:{$projectId}:roles";
            $zitadelRoles = [];
            if (isset($decoded->{$rolesClaim})) {
                $zitadelRoles = array_keys((array) $decoded->{$rolesClaim});
            }

            $email = $decoded->email ?? null;
            $sub = $decoded->sub ?? null;
            $zitadelRole = in_array('member', $zitadelRoles) ? 'member' : 'pending';

            // Try to resolve the local User model by email or zitadel_sub
            $localUser = null;
            if ($email) {
                $localUser = User::where('email', $email)->first();
            }
            if (! $localUser && $sub) {
                $localUser = User::where('zitadel_sub', $sub)->first();
            }

            if ($localUser) {
                // Merge Zitadel properties onto the local User
                $localUser->setAttribute('sub', $sub);
                $localUser->setAttribute('zitadel_roles', $zitadelRoles);

                // Resolve the effective role from authz service (cached per request)
                $localUser->setAttribute('role', $this->resolveRole($sub));

                $this->user = $localUser;
            } else {
                // No local user found — return a GenericUser (e.g., first-time login)
                $this->user = new GenericUser([
                    'id'            => $decoded->sub,
                    'sub'           => $decoded->sub,
                    'email'         => $email,
                    'name'          => $decoded->name ?? $decoded->preferred_username ?? null,
                    'role'          => $zitadelRole,
                    'roles'         => $zitadelRoles,
                ]);
            }

            return $this->user;
        } catch (\Exception $e) {
            Log::debug('Zitadel JWT validation failed: ' . $e->getMessage());
            return null;
        }
    }

    public function validate(array $credentials = []): bool
    {
        return false;
    }

    public function hasUser(): bool
    {
        return $this->user !== null;
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Resolve the user's effective role by checking the authz service.
     * Cached for the duration of the request to avoid repeated HTTP calls.
     *
     * Maps authz permissions to legacy role names so existing controller
     * code (in_array($user->role, ['admin', 'superadmin'])) keeps working.
     */
    protected function resolveRole(string $subjectId): string
    {
        try {
            return app(AuthzService::class)->resolveRole($subjectId);
        } catch (\Exception $e) {
            Log::warning('Failed to resolve authz role, defaulting to none', [
                'subject_id' => $subjectId,
                'error' => $e->getMessage(),
            ]);
            return 'none';
        }
    }

    /**
     * Fetch and cache Zitadel JWKS keys.
     */
    protected function getJwks(): array
    {
        $jwksUri = rtrim(config('zitadel.issuer'), '/') . '/oauth/v2/keys';

        $jwksJson = Cache::remember('zitadel_jwks', 3600, function () use ($jwksUri) {
            $response = file_get_contents($jwksUri);
            if ($response === false) {
                throw new \RuntimeException("Failed to fetch JWKS from {$jwksUri}");
            }
            return $response;
        });

        $jwks = json_decode($jwksJson, true);

        return JWK::parseKeySet($jwks);
    }
}
