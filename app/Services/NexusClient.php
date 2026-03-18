<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Client for the Nexus platform API.
 *
 * Nexus manages user lifecycle (create/invite users in Zitadel).
 * Auth: forwards the calling admin's Zitadel access token.
 */
class NexusClient
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('nexus.url'), '/');
    }

    /**
     * Invite a user — creates them in Zitadel and sends an invite email.
     *
     * Returns ['user_id' => string, 'created' => bool] on success.
     * Throws on failure.
     */
    public function inviteUser(string $bearerToken, string $email, string $firstName, string $lastName): array
    {
        $response = Http::acceptJson()
            ->withToken($bearerToken)
            ->post("{$this->baseUrl}/api/v1/users/invite", [
                'email'      => $email,
                'first_name' => $firstName,
                'last_name'  => $lastName,
            ]);

        if (! $response->successful()) {
            Log::error('Nexus invite user failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
                'email'  => $email,
            ]);

            $message = $response->json('error') ?? $response->json('message') ?? 'Failed to invite user';
            throw new \RuntimeException($message, $response->status());
        }

        return [
            'user_id' => $response->json('user_id'),
            'created' => $response->json('created', true),
        ];
    }

    /**
     * Search for users by email.
     *
     * @param string $method  Match method: "equals", "contains", "starts_with", "ends_with".
     * Returns array of user objects or empty array.
     */
    public function searchUserByEmail(string $bearerToken, string $email, string $method = 'equals'): array
    {
        $response = Http::acceptJson()
            ->withToken($bearerToken)
            ->get("{$this->baseUrl}/api/v1/users/search", [
                'email'  => $email,
                'method' => $method,
            ]);

        if (! $response->successful()) {
            Log::warning('Nexus user search failed', [
                'status' => $response->status(),
                'email'  => $email,
            ]);
            return [];
        }

        return $response->json('users') ?? [];
    }
}
