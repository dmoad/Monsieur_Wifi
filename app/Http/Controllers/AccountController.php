<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Location;
use App\Models\Organization;
use App\Models\User;
use App\Models\Zone;
use App\Services\AuthzClient;
use App\Services\AuthzService;
use App\Services\NexusClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    protected AuthzClient $authzClient;
    protected AuthzService $authz;
    protected NexusClient $nexus;

    public function __construct(AuthzClient $authzClient, AuthzService $authz, NexusClient $nexus)
    {
        $this->authzClient = $authzClient;
        $this->authz = $authz;
        $this->nexus = $nexus;
    }

    /**
     * Get the authenticated user's profile + permissions.
     */
    public function me(Request $request)
    {
        $user = Auth::guard('api')->user();

        $subjectId = $user->zitadel_sub ?? $user->sub ?? null;
        $orgId = $request->header('X-Org-Id') ?? $user->current_organization_id;
        $permissions = $subjectId ? $this->authz->getPermissions($subjectId) : [];
        $role = $subjectId ? $this->authz->resolveRole($subjectId, $orgId ? (string) $orgId : null) : 'none';

        // Platform role from local DB (superadmin, admin, user)
        // Org role from authz (owner, admin, operator, viewer, partner)
        $platformRole = $user->role ?? 'user';

        // Resolve org entitlements (plan features + limits)
        $org = $orgId ? Organization::find($orgId) : null;
        $entitlements = $this->resolveEntitlements($org);
        $features = $this->resolveFeatures($platformRole, $role, $entitlements);

        return response()->json([
            'id'                      => $user->id,
            'sub'                     => $user->sub ?? null,
            'email'                   => $user->email ?? null,
            'name'                    => $user->name ?? null,
            'role'                    => $role,
            'platform_role'           => $platformRole,
            'features'                => $features,
            'entitlements'            => $entitlements,
            'current_organization_id' => $user->current_organization_id,
            'permissions'             => $permissions,
        ]);
    }

    /**
     * Resolve org entitlements from the plan.
     */
    private function resolveEntitlements(?Organization $org): array
    {
        if (! $org) {
            $defaultPlan = config('plans.free');
            return [
                'plan'   => 'free',
                'label'  => $defaultPlan['label'] ?? 'Free',
                'limits' => $defaultPlan['limits'] ?? [],
                'addons' => [],
            ];
        }

        return [
            'plan'   => $org->plan ?? 'free',
            'label'  => $org->getPlanConfig()['label'] ?? $org->plan,
            'limits' => $org->getLimits(),
            'addons' => $org->features_override ?? [],
        ];
    }

    /**
     * Resolve which features/pages the user can access.
     *
     * Three layers:
     *   1. Plan features — what the org's plan unlocks (entitlements)
     *   2. RBAC features — what the user's role allows (org role gates team, domain-blocking, etc.)
     *   3. Platform features — admin/superadmin panels (Digilan staff only)
     */
    private function resolveFeatures(string $platformRole, string $orgRole, array $entitlements): array
    {
        // Start with what the org's plan allows
        $planFeatures = $entitlements['addons'] ?? [];
        $planConfig = config("plans.{$entitlements['plan']}", config('plans.free'));
        $planFeatures = array_unique(array_merge($planConfig['features'] ?? [], $planFeatures));

        // RBAC layer: restrict some plan features by org role
        $features = [];
        foreach ($planFeatures as $f) {
            // team + domain-blocking require org owner or admin
            if (in_array($f, ['team', 'domain-blocking']) && ! in_array($orgRole, ['owner', 'admin'])) {
                continue;
            }
            $features[] = $f;
        }

        // Platform admin features (not plan-gated — Digilan staff only)
        if (in_array($platformRole, ['admin', 'superadmin'])) {
            $features[] = 'shop';
            $features[] = 'orders';
            $features[] = 'admin.team';
            $features[] = 'admin.models';
            $features[] = 'admin.inventory';
            $features[] = 'admin.orders';
            $features[] = 'admin.qos';
        }

        if ($platformRole === 'superadmin') {
            $features[] = 'admin.firmware';
            $features[] = 'admin.system-settings';
        }

        return array_values(array_unique($features));
    }

    /**
     * Upload a profile picture.
     */
    public function uploadProfilePicture(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if (! $request->hasFile('file')) {
            return response()->json(['error' => 'No file provided'], 400);
        }

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());

        if (! in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return response()->json(['error' => 'Invalid file type'], 400);
        }

        if ($file->getSize() > 2 * 1024 * 1024) {
            return response()->json(['error' => 'File size must be less than 2MB'], 400);
        }

        $filename = time() . '.' . $extension;
        $file->move(public_path('uploads/profile_pictures'), $filename);

        return response()->json([
            'message'  => 'Profile picture uploaded successfully',
            'filename' => $filename,
        ]);
    }

    /**
     * List all available roles from config.
     */
    public function roles()
    {
        $roles = collect(config('rbac.roles'))->map(function ($role) {
            return [
                'id'          => $role['id'],
                'name'        => $role['name'],
                'alias'       => $role['alias'],
                'description' => $role['description'],
            ];
        });

        return response()->json(['roles' => $roles]);
    }

    /**
     * List team members with their roles and scopes.
     *
     * Uses listUserPermissions (per user) to get ALL ACL entries including
     * specific target_ids, not just wildcard ones.
     */
    public function listUsers(Request $request)
    {
        // Step 1: Discover subject_ids from ACL entries
        $subjectIds = collect();
        $targets = config('rbac.targets', []);

        foreach ($targets as $target) {
            $holders = $this->authzClient->listRoleHolders($target, '*');
            foreach ($holders as $h) {
                $subjectIds->push($h['subject_id']);
            }
        }

        // Step 2: Also include all local users with a zitadel_sub (they belong to the org)
        $localUsers = User::whereNotNull('zitadel_sub')->get();
        foreach ($localUsers as $lu) {
            $subjectIds->push($lu->zitadel_sub);
        }

        $subjectIds = $subjectIds->unique();
        $localUsersKeyed = $localUsers->keyBy('zitadel_sub');

        // Step 3: For each subject, fetch full permissions from authz
        $validTargets = array_values($targets);
        $allPerms = [];
        $userPermsMap = [];

        foreach ($subjectIds as $subjectId) {
            $this->authz->flushPermissions($subjectId);
            $permissions = $this->authzClient->listUserPermissions($subjectId);
            $filtered = collect($permissions)->filter(fn($p) => in_array($p['target'] ?? null, $validTargets, true))->values()->toArray();
            $userPermsMap[$subjectId] = $filtered;
            $allPerms = array_merge($allPerms, $filtered);
        }

        $targetNames = $this->resolveTargetNames($allPerms);

        $users = $subjectIds->map(function ($subjectId) use ($localUsersKeyed, $userPermsMap, $targetNames) {
            $localUser = $localUsersKeyed[$subjectId] ?? null;
            $role = $this->authz->resolveRole($subjectId);

            return [
                'subject_id'  => $subjectId,
                'name'        => $localUser->name ?? $subjectId,
                'email'       => $localUser->email ?? null,
                'role'        => $role,
                'permissions' => collect($userPermsMap[$subjectId] ?? [])->map(function ($p) use ($targetNames) {
                    $target = $p['target'] ?? null;
                    $targetId = $p['target_id'] ?? null;
                    return [
                        'role_id'     => $p['role_id'] ?? null,
                        'role_name'   => $p['role_name'] ?? null,
                        'target'      => $target,
                        'target_id'   => $targetId,
                        'target_name' => $targetNames[$target][(string) $targetId] ?? null,
                    ];
                })->values()->toArray(),
            ];
        })->values();

        return response()->json([
            'status' => 'success',
            'users'  => $users,
            'total'  => $users->count(),
        ]);
    }

    /**
     * List all ACL entries across all targets (flat, not grouped).
     *
     * Uses listUserPermissions to get complete entries with target + target_id.
     */
    public function listPermissions(Request $request)
    {
        $filterTarget = $request->query('target');

        // Step 1: Discover all subject_ids via role holders + local users
        $subjectIds = collect();
        $targets = config('rbac.targets', []);

        foreach ($targets as $target) {
            $holders = $this->authzClient->listRoleHolders($target, '*');
            foreach ($holders as $h) {
                $subjectIds->push($h['subject_id']);
            }
        }

        // Also include all local users with a zitadel_sub
        $localUsers = User::whereNotNull('zitadel_sub')->pluck('zitadel_sub');
        $subjectIds = $subjectIds->merge($localUsers)->unique();

        // Step 2: For each subject, fetch full permissions (only known targets)
        $validTargets = array_values($targets);
        $allEntries = collect();
        foreach ($subjectIds as $sid) {
            $perms = $this->authzClient->listUserPermissions($sid);
            foreach ($perms as $p) {
                $permTarget = $p['target'] ?? null;
                // Skip legacy/unknown targets
                if (! in_array($permTarget, $validTargets, true)) {
                    continue;
                }
                if ($filterTarget && $permTarget !== $filterTarget) {
                    continue;
                }
                $allEntries->push(array_merge($p, ['subject_id' => $sid]));
            }
        }

        // Resolve target IDs to names
        $targetNames = $this->resolveTargetNames($allEntries->toArray());

        // Enrich with local user info
        $localUsers = User::whereIn('zitadel_sub', $subjectIds)->get()->keyBy('zitadel_sub');

        $permissions = $allEntries->map(function ($e) use ($localUsers, $targetNames) {
            $localUser = $localUsers[$e['subject_id']] ?? null;
            $target = $e['target'] ?? null;
            $targetId = $e['target_id'] ?? null;
            return [
                'subject_id'  => $e['subject_id'],
                'name'        => $localUser->name ?? $e['subject_id'],
                'email'       => $localUser->email ?? null,
                'role_id'     => $e['role_id'] ?? null,
                'role_name'   => $e['role_name'] ?? null,
                'target'      => $target,
                'target_id'   => $targetId,
                'target_name' => $targetNames[$target][(string) $targetId] ?? null,
            ];
        })->values();

        return response()->json([
            'status'      => 'success',
            'permissions' => $permissions,
            'total'       => $permissions->count(),
        ]);
    }

    /**
     * Get permissions for a specific user.
     */
    public function userPermissions(string $subjectId)
    {
        // Always return fresh data (bypass 60s cache)
        $this->authz->flushPermissions($subjectId);

        $validTargets = array_values(config('rbac.targets', []));
        $perms = collect($this->authz->getPermissions($subjectId))
            ->filter(fn($p) => in_array($p['target'] ?? null, $validTargets, true))
            ->values();

        $targetNames = $this->resolveTargetNames($perms->toArray());

        $permissions = $perms->map(function ($p) use ($targetNames) {
            $target = $p['target'] ?? null;
            $targetId = $p['target_id'] ?? null;
            $p['target_name'] = $targetNames[$target][(string) $targetId] ?? null;
            return $p;
        })->values();

        return response()->json([
            'subject_id'  => $subjectId,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Invite a new team member.
     *
     * 1. Calls Nexus to create the user in Zitadel (sends invite email)
     * 2. Creates a local User record with the Zitadel sub
     * 3. Assigns the requested role in authz
     *
     * POST /api/accounts/invite  { email, first_name, last_name, role_id, target, target_id }
     */
    public function invite(Request $request)
    {
        $request->validate([
            'email'      => 'required|email',
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'role_id'    => 'required|integer',
            'target'     => 'required|string',
            'target_id'  => 'required|string',
        ]);

        $adminToken = $request->bearerToken();

        // Step 1: Create user in Zitadel via Nexus
        try {
            $nexusResult = $this->nexus->inviteUser(
                $adminToken,
                $request->input('email'),
                $request->input('first_name'),
                $request->input('last_name'),
            );
        } catch (\RuntimeException $e) {
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            return response()->json(['error' => $e->getMessage()], $statusCode);
        }

        $zitadelSub = $nexusResult['user_id'];

        // Step 2: Create local user record
        $fullName = trim($request->input('first_name') . ' ' . $request->input('last_name'));
        User::firstOrCreate(
            ['email' => $request->input('email')],
            [
                'name'              => $fullName,
                'zitadel_sub'       => $zitadelSub,
                'password'          => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
            ]
        );

        // Step 3: Assign role in authz
        $ok = $this->authzClient->assignRole(
            $zitadelSub,
            $request->input('role_id'),
            $request->input('target'),
            $request->input('target_id'),
        );

        if (! $ok) {
            return response()->json(['error' => 'User created in Zitadel but role assignment failed'], 500);
        }

        return response()->json([
            'message'    => 'Member invited successfully',
            'user_id'    => $zitadelSub,
            'created'    => $nexusResult['created'],
        ], $nexusResult['created'] ? 201 : 200);
    }

    /**
     * Search for a user in Zitadel by email (via Nexus).
     *
     * GET /api/accounts/search?email=...
     */
    public function searchZitadelUser(Request $request)
    {
        $request->validate(['email' => 'required|string|min:3']);

        $adminToken = $request->bearerToken();
        $results = $this->nexus->searchUserByEmail($adminToken, $request->query('email'), 'contains');

        return response()->json([
            'users' => collect($results)->map(fn($u) => [
                'id'         => $u['id'] ?? null,
                'email'      => $u['email'] ?? null,
                'first_name' => $u['first_name'] ?? null,
                'last_name'  => $u['last_name'] ?? null,
                'state'      => $u['state'] ?? null,
            ])->values(),
        ]);
    }

    /**
     * Link an existing Zitadel user to this app (no invite email).
     *
     * 1. Searches Nexus for the user by email
     * 2. Creates a local User record
     * 3. Assigns the requested role in authz
     *
     * POST /api/accounts/link  { email, role_id, target, target_id }
     */
    public function link(Request $request)
    {
        $request->validate([
            'email'     => 'required|email',
            'role_id'   => 'required|integer',
            'target'    => 'required|string',
            'target_id' => 'required|string',
        ]);

        $adminToken = $request->bearerToken();
        $email = $request->input('email');

        // Step 1: Search for user in Zitadel via Nexus
        $results = $this->nexus->searchUserByEmail($adminToken, $email);

        if (empty($results)) {
            return response()->json([
                'error' => 'No Zitadel account found for this email. Use "Invite" to create a new account.',
            ], 404);
        }

        $zitadelUser = $results[0];
        $zitadelSub = $zitadelUser['id'];
        $fullName = trim(($zitadelUser['first_name'] ?? '') . ' ' . ($zitadelUser['last_name'] ?? ''));

        // Step 2: Create local user record
        User::firstOrCreate(
            ['email' => $email],
            [
                'name'              => $fullName ?: $email,
                'zitadel_sub'       => $zitadelSub,
                'password'          => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
            ]
        );

        // Step 3: Assign role in authz
        $ok = $this->authzClient->assignRole(
            $zitadelSub,
            $request->input('role_id'),
            $request->input('target'),
            $request->input('target_id'),
        );

        if (! $ok) {
            return response()->json(['error' => 'User linked locally but role assignment failed'], 500);
        }

        return response()->json([
            'message' => 'Existing user linked successfully',
            'user_id' => $zitadelSub,
            'name'    => $fullName,
        ]);
    }

    /**
     * Assign a role to a user on a target scope.
     */
    public function assignRole(Request $request, string $subjectId)
    {
        $request->validate([
            'role_id'   => 'required|integer',
            'target'    => 'required|string',
            'target_id' => 'required|string',
        ]);

        $ok = $this->authzClient->assignRole(
            $subjectId,
            $request->input('role_id'),
            $request->input('target'),
            $request->input('target_id'),
        );

        if (! $ok) {
            return response()->json(['error' => 'Failed to assign role'], 500);
        }

        // Flush cached permissions so the change takes effect immediately
        $this->authz->flushPermissions($subjectId);

        return response()->json(['message' => 'Role assigned']);
    }

    /**
     * Revoke a role from a user.
     */
    public function revokeRole(Request $request, string $subjectId)
    {
        $request->validate([
            'role_id'   => 'required|integer',
            'target'    => 'required|string',
            'target_id' => 'required|string',
        ]);

        $ok = $this->authzClient->revokeRole(
            $subjectId,
            $request->input('role_id'),
            $request->input('target'),
            $request->input('target_id'),
        );

        if (! $ok) {
            return response()->json(['error' => 'Failed to revoke role'], 500);
        }

        $this->authz->flushPermissions($subjectId);

        return response()->json(['message' => 'Role revoked']);
    }

    /**
     * Resolve target IDs to human-readable names.
     *
     * Returns a map like ['mrwifi:zone' => ['2' => 'Paris Zone', ...], ...]
     */
    private function resolveTargetNames(array $permissions): array
    {
        $targets = config('rbac.targets', []);
        $lookup = [];

        // Group IDs by target type
        $idsByTarget = [];
        foreach ($permissions as $p) {
            $target = $p['target'] ?? null;
            $targetId = $p['target_id'] ?? null;
            if ($target && $targetId && $targetId !== '*') {
                $idsByTarget[$target][] = $targetId;
            }
        }

        // Resolve each target type
        $modelMap = [
            $targets['org'] ?? null      => Organization::class,
            $targets['zone'] ?? null     => Zone::class,
            $targets['location'] ?? null => Location::class,
            $targets['device'] ?? null   => Device::class,
        ];

        foreach ($idsByTarget as $target => $ids) {
            $model = $modelMap[$target] ?? null;
            if ($model) {
                $names = $model::whereIn('id', array_unique($ids))->pluck('name', 'id');
                foreach ($names as $id => $name) {
                    $lookup[$target][(string) $id] = $name;
                }
            }
        }

        return $lookup;
    }
}
