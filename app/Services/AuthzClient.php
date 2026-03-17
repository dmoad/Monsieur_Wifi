<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AuthzClient
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('authz.url'), '/');
        $this->apiKey = config('authz.api_key', '');
    }

    protected function http(): \Illuminate\Http\Client\PendingRequest
    {
        $request = Http::acceptJson();
        if ($this->apiKey !== '') {
            $request = $request->withToken($this->apiKey);
        }
        return $request;
    }

    /**
     * Check if a user is allowed to perform actions on a target.
     */
    public function isAllowed(string $subjectId, string $target, string $targetId, array $actions): bool
    {
        $response = $this->http()->post("{$this->baseUrl}/v1/is-allowed", [
            'subject_id' => $subjectId,
            'target'        => $target,
            'target_id'     => $targetId,
            'actions'       => $actions,
        ]);

        return $response->successful() && ($response->json('allowed') === true);
    }

    /**
     * Assign a role to a user on a target.
     */
    public function assignRole(string $subjectId, int $roleId, string $target, string $targetId): bool
    {
        $response = $this->http()->post("{$this->baseUrl}/v1/assign-role", [
            'subject_id' => $subjectId,
            'role_id'       => $roleId,
            'target'        => $target,
            'target_id'     => $targetId,
        ]);

        return $response->successful();
    }

    /**
     * Revoke a role from a user on a target.
     */
    public function revokeRole(string $subjectId, int $roleId, string $target, string $targetId): bool
    {
        $response = $this->http()->post("{$this->baseUrl}/v1/revoke-role", [
            'subject_id' => $subjectId,
            'role_id'       => $roleId,
            'target'        => $target,
            'target_id'     => $targetId,
        ]);

        return $response->successful();
    }

    /**
     * Seed roles (idempotent).
     */
    public function seedRoles(array $roles): array
    {
        $response = $this->http()->post("{$this->baseUrl}/v1/seed-roles", [
            'roles' => $roles,
        ]);

        return $response->json() ?? [];
    }

    /**
     * List all available roles.
     */
    public function listRoles(): array
    {
        $response = $this->http()->get("{$this->baseUrl}/v1/roles");

        return $response->json('roles') ?? [];
    }

    /**
     * List all permissions for a user.
     */
    public function listUserPermissions(string $subjectId): array
    {
        $response = $this->http()->get("{$this->baseUrl}/v1/user-permissions", [
            'subject_id' => $subjectId,
        ]);

        return $response->json('permissions') ?? [];
    }

    /**
     * List all users that have access to a specific resource.
     */
    public function listRoleHolders(string $target, string $targetId): array
    {
        $response = $this->http()->get("{$this->baseUrl}/v1/role-holders", [
            'target'    => $target,
            'target_id' => $targetId,
        ]);

        return $response->json('holders') ?? [];
    }
}
