<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Services\AuthzClient;
use App\Services\AuthzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrganizationController extends Controller
{
    protected AuthzService $authz;

    public function __construct(AuthzService $authz)
    {
        $this->authz = $authz;
    }

    /**
     * List all organizations the current user has access to.
     */
    public function index(Request $request)
    {
        $user = Auth::guard('api')->user();
        $sub = $user->zitadel_sub;

        // Get org IDs from authz permissions
        $orgIds = $this->authz->getAccessibleOrgIds($sub);

        // Also include orgs the user owns (for backward compat with wildcard permissions)
        $ownedOrgIds = Organization::where('owner_id', $user->id)->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        $allOrgIds = array_unique(array_merge($orgIds, $ownedOrgIds));

        $orgs = Organization::whereIn('id', $allOrgIds)
            ->select('id', 'name', 'slug')
            ->get()
            ->map(function ($org) use ($sub) {
                return [
                    'id'   => $org->id,
                    'name' => $org->name,
                    'slug' => $org->slug,
                    'role' => $this->authz->resolveRole($sub, (string) $org->id),
                ];
            });

        return response()->json([
            'organizations' => $orgs,
            'current_id'    => $user->current_organization_id,
        ]);
    }

    /**
     * Switch to a different organization.
     */
    public function switch(Request $request)
    {
        $request->validate(['organization_id' => 'required|exists:organizations,id']);

        $user = Auth::guard('api')->user();
        $sub = $user->zitadel_sub;
        $orgId = (string) $request->input('organization_id');

        // Verify user has access to this org
        if (! $this->authz->can($sub, config('rbac.targets.org'), $orgId, 'read')) {
            return response()->json(['error' => 'You do not have access to this organization'], 403);
        }

        $user->newQuery()->where('id', $user->id)->update(['current_organization_id' => $orgId]);

        $org = Organization::find($orgId);

        return response()->json([
            'organization' => [
                'id'   => $org->id,
                'name' => $org->name,
                'slug' => $org->slug,
                'role' => $this->authz->resolveRole($sub, $orgId),
            ],
        ]);
    }

    /**
     * Create a new organization.
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $user = Auth::guard('api')->user();
        $sub = $user->zitadel_sub;

        $org = Organization::create([
            'name'     => $request->input('name'),
            'owner_id' => $user->id,
        ]);

        // Assign owner role via authz
        try {
            $authz = app(AuthzClient::class);
            $authz->assignRole($sub, 1, config('rbac.targets.org'), (string) $org->id);
        } catch (\Exception $e) {
            Log::warning('Failed to assign owner role on new org', ['org_id' => $org->id, 'error' => $e->getMessage()]);
        }

        return response()->json([
            'organization' => [
                'id'   => $org->id,
                'name' => $org->name,
                'slug' => $org->slug,
                'role' => 'owner',
            ],
        ], 201);
    }

    /**
     * Rename a specific organization.
     */
    public function rename(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $user = Auth::guard('api')->user();
        $sub = $user->zitadel_sub;

        if (! $this->authz->can($sub, config('rbac.targets.org'), (string) $id, 'manage')) {
            return response()->json(['error' => 'You do not have permission to rename this organization'], 403);
        }

        $org = Organization::findOrFail($id);
        $org->update(['name' => $request->input('name')]);

        return response()->json(['organization' => $org]);
    }

    /**
     * Update the current organization's name.
     */
    public function update(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $user = Auth::guard('api')->user();
        $orgId = $user->current_organization_id;

        if (! $orgId) {
            return response()->json(['error' => 'No current organization'], 400);
        }

        $org = Organization::findOrFail($orgId);
        $org->update(['name' => $request->input('name')]);

        return response()->json(['organization' => $org]);
    }
}
