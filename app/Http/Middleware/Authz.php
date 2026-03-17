<?php

namespace App\Http\Middleware;

use App\Services\AuthzService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Authz
{
    protected AuthzService $authz;

    public function __construct(AuthzService $authz)
    {
        $this->authz = $authz;
    }

    /**
     * Check if the authenticated user has the required permission via the authz service.
     *
     * Uses hierarchical checks: if a user has access at org/zone level,
     * they implicitly have access at location/device level.
     *
     * Usage in routes:
     *   ->middleware('authz:mrwifi:location,read')          // wildcard target_id
     *   ->middleware('authz:mrwifi:location,write,{id}')    // target_id from route param
     *   ->middleware('authz:mrwifi:org,manage')              // org-level check
     */
    public function handle(Request $request, Closure $next, string $target, string $action, string $targetId = '*'): Response
    {
        $user = Auth::guard('api')->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $subjectId = $user->zitadel_sub ?? $user->sub ?? null;
        if (! $subjectId) {
            return response()->json(['error' => 'No subject ID'], 401);
        }

        // Resolve target_id: if wrapped in braces, pull from route params.
        $resolvedTargetId = $targetId;
        if (preg_match('/^\{(.+)\}$/', $targetId, $matches)) {
            $resolvedTargetId = $request->route($matches[1]) ?? '*';
        }

        // For org-level checks with wildcard, use the user's current org
        if ($target === config('rbac.targets.org') && $resolvedTargetId === '*') {
            $orgId = $request->header('X-Org-Id')
                ?? $user->current_organization_id;

            if ($orgId) {
                $resolvedTargetId = (string) $orgId;
            }
        }

        if (! $this->authz->can($subjectId, $target, $resolvedTargetId, $action)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
