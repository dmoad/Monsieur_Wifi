<?php

namespace App\Services;

use App\Models\Device;
use App\Models\Location;
use App\Models\Organization;
use App\Models\Zone;
use Illuminate\Support\Facades\Cache;

/**
 * High-level authorization service with hierarchical permission checks.
 *
 * Caches the full permission set per user and resolves the resource hierarchy
 * (org > zone > location > device) in-memory.
 */
class AuthzService
{
    protected AuthzClient $client;

    public function __construct(AuthzClient $client)
    {
        $this->client = $client;
    }

    /**
     * Check if a subject can perform an action on a resource,
     * walking up the hierarchy: device → location → zone → org.
     */
    public function can(string $subjectId, string $target, string $targetId, string $action): bool
    {
        $permissions = $this->getPermissions($subjectId);

        // Build the chain of (target, targetId) pairs to check
        $chain = $this->buildHierarchyChain($target, $targetId);

        foreach ($chain as [$checkTarget, $checkTargetId]) {
            if ($this->matchesPermission($permissions, $checkTarget, $checkTargetId, $action)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Resolve a display role name for the user, optionally scoped to an org.
     */
    public function resolveRole(string $subjectId, ?string $orgId = null): string
    {
        $permissions = $this->getPermissions($subjectId);

        // If org-scoped, filter to permissions relevant to this org
        if ($orgId) {
            $permissions = $this->filterPermissionsForOrg($permissions, $orgId);
        }

        // Owner at org level
        foreach ($permissions as $p) {
            if (($p['target'] ?? '') === 'mrwifi:org') {
                $actions = $this->extractActions($p);
                if (in_array('*', $actions)) {
                    return 'owner';
                }
            }
        }

        // Admin at org level
        foreach ($permissions as $p) {
            if (($p['target'] ?? '') === 'mrwifi:org') {
                $actions = $this->extractActions($p);
                if (in_array('manage', $actions)) {
                    return 'admin';
                }
            }
        }

        // Partner — has read on org + write on devices but no manage on org
        foreach ($permissions as $p) {
            if (($p['role_name'] ?? '') === 'partner') {
                return 'partner';
            }
        }

        // Operator — has write on any zone/location/device
        foreach ($permissions as $p) {
            $target = $p['target'] ?? '';
            $actions = $this->extractActions($p);
            if (in_array($target, ['mrwifi:zone', 'mrwifi:location', 'mrwifi:device'])) {
                if (in_array('write', $actions) || in_array('*', $actions)) {
                    return 'operator';
                }
            }
        }

        // Viewer — has any permission at all
        if (! empty($permissions)) {
            return 'viewer';
        }

        return 'none';
    }

    /**
     * Filter permissions to those relevant to a specific org.
     */
    private function filterPermissionsForOrg(array $permissions, string $orgId): array
    {
        return array_filter($permissions, function ($p) use ($orgId) {
            $target = $p['target'] ?? '';
            $targetId = $p['target_id'] ?? '';

            // Org-level: must match this org or wildcard
            if ($target === 'mrwifi:org') {
                return $targetId === $orgId || $targetId === '*';
            }

            // Sub-org resources: check if they belong to this org
            // For now, include all non-org permissions (they'll be filtered at query time)
            return true;
        });
    }

    /**
     * Get all permissions for a subject, cached for 60 seconds.
     */
    public function getPermissions(string $subjectId): array
    {
        $cacheKey = "authz:permissions:{$subjectId}";

        return Cache::remember($cacheKey, 60, function () use ($subjectId) {
            return $this->client->listUserPermissions($subjectId);
        });
    }

    /**
     * Invalidate cached permissions for a subject (call after role changes).
     */
    public function flushPermissions(string $subjectId): void
    {
        Cache::forget("authz:permissions:{$subjectId}");
    }

    /**
     * Get all org IDs a user has access to (from their permissions).
     *
     * @return array<string> org IDs
     */
    public function getAccessibleOrgIds(string $subjectId): array
    {
        $permissions = $this->getPermissions($subjectId);
        $orgIds = [];

        foreach ($permissions as $p) {
            if (($p['target'] ?? '') === 'mrwifi:org') {
                $targetId = $p['target_id'] ?? '';
                if ($targetId === '*') {
                    // Wildcard = access to all orgs
                    return Organization::pluck('id')->map(fn ($id) => (string) $id)->toArray();
                }
                $orgIds[] = $targetId;
            }
        }

        return array_unique($orgIds);
    }

    /**
     * Build the hierarchy chain for a given resource.
     *
     * Returns an array of [target, targetId] pairs from most specific to least:
     *   device:5 → location:12 → zone:3 → org:*
     */
    private function buildHierarchyChain(string $target, string $targetId): array
    {
        $chain = [[$target, $targetId]];

        // Also check wildcard for same target
        if ($targetId !== '*') {
            $chain[] = [$target, '*'];
        }

        $orgId = null;

        switch ($target) {
            case 'mrwifi:device':
                $location = $this->getDeviceLocation($targetId);
                if ($location) {
                    $chain[] = ['mrwifi:location', (string) $location->id];
                    $chain[] = ['mrwifi:location', '*'];
                    if ($location->zone_id) {
                        $chain[] = ['mrwifi:zone', (string) $location->zone_id];
                        $chain[] = ['mrwifi:zone', '*'];
                    }
                    $orgId = $location->organization_id;
                }
                break;

            case 'mrwifi:location':
                $location = Location::find($targetId);
                if ($location) {
                    if ($location->zone_id) {
                        $chain[] = ['mrwifi:zone', (string) $location->zone_id];
                        $chain[] = ['mrwifi:zone', '*'];
                    }
                    $orgId = $location->organization_id;
                }
                break;

            case 'mrwifi:zone':
                $zone = Zone::find($targetId);
                if ($zone) {
                    $orgId = $zone->organization_id;
                }
                break;

            case 'mrwifi:org':
                // Already at the top
                break;
        }

        // Resolve to the actual org, then also check wildcard
        if ($target !== 'mrwifi:org') {
            if ($orgId) {
                $chain[] = ['mrwifi:org', (string) $orgId];
            }
            $chain[] = ['mrwifi:org', '*'];
        }

        return $chain;
    }

    /**
     * Check if any permission in the set matches the target + action.
     */
    private function matchesPermission(array $permissions, string $target, string $targetId, string $action): bool
    {
        foreach ($permissions as $p) {
            $pTarget = $p['target'] ?? '';
            $pTargetId = $p['target_id'] ?? '';

            if ($pTarget !== $target) {
                continue;
            }

            if ($pTargetId !== '*' && $pTargetId !== $targetId) {
                continue;
            }

            // Check if the role grants this action
            $actions = $this->extractActions($p);
            if (in_array('*', $actions) || in_array($action, $actions)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract actions from a permission entry.
     * The authz service may return actions as a flat array or nested in role_actions.
     */
    private function extractActions(array $permission): array
    {
        // Direct actions array
        if (isset($permission['actions'])) {
            return (array) $permission['actions'];
        }

        // Role-based: the role_name implies actions via the role_actions table.
        // Since we seeded roles, we can derive from role name.
        $roleName = $permission['role_name'] ?? '';

        return $this->actionsForRole($roleName, $permission['target'] ?? '');
    }

    /**
     * Get the actions a role grants on a target, from config.
     */
    private function actionsForRole(string $roleName, string $target): array
    {
        static $cache = [];

        $key = "{$roleName}:{$target}";
        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $roles = config('rbac.roles', []);
        foreach ($roles as $role) {
            if ($role['name'] === $roleName) {
                $actions = [];
                foreach ($role['actions'] as $a) {
                    if ($a['target'] === $target) {
                        $actions[] = $a['action'];
                    }
                }
                $cache[$key] = $actions;
                return $actions;
            }
        }

        $cache[$key] = [];
        return [];
    }

    /**
     * Get the location that a device belongs to.
     */
    private function getDeviceLocation(string $deviceId): ?Location
    {
        return Location::where('device_id', $deviceId)->first();
    }
}
