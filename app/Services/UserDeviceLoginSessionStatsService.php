<?php

namespace App\Services;

use App\Models\GuestNetworkUser;
use App\Models\LocationNetwork;
use App\Models\UserDeviceLoginSession;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Log;

class UserDeviceLoginSessionStatsService
{
    public function normalizeStatus(mixed $raw): ?string
    {
        if ($raw === null) {
            return null;
        }
        if (is_numeric($raw)) {
            return match ((int) $raw) {
                1 => 'start',
                2 => 'stop',
                3 => 'interim',
                default => null,
            };
        }
        $s = strtolower(trim((string) $raw));
        if (str_contains($s, 'interim')) {
            return 'interim';
        }
        if (str_contains($s, 'stop')) {
            return 'stop';
        }
        if (str_contains($s, 'start')) {
            return 'start';
        }

        return null;
    }

    /**
     * @return list<string>
     */
    public function macAddressVariants(string $username): array
    {
        $hex = preg_replace('/[^a-fA-F0-9]/', '', $username);
        $variants = array_filter(array_unique(array_merge(
            [$username],
            strlen($hex) === 12
                ? [
                    strtolower(implode(':', str_split($hex, 2))),
                    strtolower(implode('-', str_split($hex, 2))),
                    strtolower($hex),
                    strtoupper($hex),
                ]
                : []
        )));

        return array_values($variants);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function createRoamingSession(array $validated): UserDeviceLoginSession
    {
        // Raw username is the canonical AA-BB-CC-11-22-33 storage format
        $macAddress = $validated['username'];
        $variants   = $this->macAddressVariants($macAddress);

        $connectTime = $this->parseFlexibleDateTime($validated['acct_start_time'] ?? null)
            ?? Carbon::now();

        $parsed     = $this->parseNasId($validated['nas_id'] ?? null);
        $zoneId     = $parsed['zone_id'];
        $locationId = $parsed['location_id'] ?? $validated['location_id'] ?? null;
        $networkId  = $parsed['network_id'] ?? $validated['network_id'] ?? null;

        // Legacy fallback: derive location_id from network when nas_id is absent
        if ($locationId === null && $networkId !== null) {
            $locationId = LocationNetwork::find($networkId)?->location_id;
        }

        $guest = $this->findOrCreateGuestNetworkUser($variants, $macAddress, $locationId, $networkId, $zoneId);

        $session = UserDeviceLoginSession::create([
            'guest_network_user_id' => $guest->id,
            'mac_address'           => $macAddress,
            'location_id'           => $locationId,
            'network_id'            => $networkId,
            'zone_id'               => $guest->zone_id ?? 0,
            'login_type'            => 'roaming',
            'login_success'         => true,
            'connect_time'          => $connectTime,
        ]);

        return $session;
    }

    /**
     * Parse a NAS-Identifier triple (zone_id-location_id-network_id) into its components.
     *
     * @return array{zone_id: int|null, location_id: int|null, network_id: int|null}
     */
    private function parseNasId(?string $nasId): array
    {
        if ($nasId !== null && preg_match('/^(\d+)-(\d+)-(\d+)$/', $nasId, $m)) {
            return [
                'zone_id'     => (int) $m[1],
                'location_id' => (int) $m[2],
                'network_id'  => (int) $m[3],
            ];
        }

        return ['zone_id' => null, 'location_id' => null, 'network_id' => null];
    }

    /**
     * @param  list<string>  $macVariants
     */
    protected function findOrCreateGuestNetworkUser(
        array $macVariants,
        string $macAddress,
        ?int $locationId,
        ?int $networkId,
        ?int $zoneId,
    ): GuestNetworkUser {
        $lookup = $macVariants ?: [$macAddress];

        // Step 1 — exact match on location + zone
        $query = GuestNetworkUser::query()->whereIn('mac_address', $lookup);
        if ($locationId !== null) {
            $query->where('location_id', $locationId);
        }
        if ($zoneId !== null) {
            $query->where('zone_id', $zoneId);
        }
        $guest = $query->first();
        if ($guest !== null) {
            return $guest;
        }

        // Step 2 — zone-only match: user is known in this zone but at a different location → copy row with new location
        if ($zoneId !== null) {
            $byZone = GuestNetworkUser::query()
                ->whereIn('mac_address', $lookup)
                ->where('zone_id', $zoneId)
                ->first();

            if ($byZone !== null) {
                return GuestNetworkUser::create(array_merge(
                    $byZone->only($byZone->getFillable()),
                    ['location_id' => $locationId, 'network_id' => $networkId]
                ));
            }
        }

        // Step 3 — brand-new roaming device: create a minimal guest record
        return GuestNetworkUser::create([
            'mac_address' => $macAddress,
            'location_id' => $locationId,
            'network_id'  => $networkId,
            'zone_id'     => $zoneId ?? 0,
            'blocked'     => false,
        ]);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function findSession(array $validated): ?UserDeviceLoginSession
    {
        $acctSessionId = $validated['acct_session_id'];
        $username      = $validated['username'];
        $locationId    = Arr::get($validated, 'location_id');
        $networkId     = Arr::get($validated, 'network_id');

        $byAcct = UserDeviceLoginSession::query()
            ->where('radius_session_id', $acctSessionId)
            ->first();

        if ($byAcct !== null) {
            return $byAcct;
        }

        $variants = $this->macAddressVariants($username);
        if ($variants === []) {
            return null;
        }

        $query = UserDeviceLoginSession::query()
            ->whereNull('disconnect_time')
            ->whereIn('mac_address', $variants);

        if ($locationId !== null) {
            $query->where('location_id', $locationId);
        }
        if ($networkId !== null) {
            $query->where('network_id', $networkId);
        }

        return $query->orderByDesc('connect_time')->first();
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function applyStats(UserDeviceLoginSession $session, string $status, array $validated): UserDeviceLoginSession
    {
        Log::info('applyStats:===============================================>>>>>>> Status::::====>>> ' . $status);
        $acctSessionId = $validated['acct_session_id'];

        if ($session->radius_session_id !== null
            && $session->radius_session_id !== $acctSessionId
            && $status === 'start') {
            abort(409, 'Session already linked to a different Acct-Session-Id');
        }

        $session->radius_session_id = $acctSessionId;

        if (array_key_exists('acct_output_octets', $validated) && $validated['acct_output_octets'] !== null) {
            $session->total_upload = (int) $validated['acct_output_octets'];
        }

        if (array_key_exists('acct_input_octets', $validated) && $validated['acct_input_octets'] !== null) {
            $session->total_download = (int) $validated['acct_input_octets'];
        }

        if (array_key_exists('acct_session_time', $validated) && $validated['acct_session_time'] !== null) {
            $session->session_duration = (int) $validated['acct_session_time'];
        }

        if ($status === 'start' && array_key_exists('acct_start_time', $validated) && $validated['acct_start_time'] !== null) {
            $ct = $this->parseFlexibleDateTime($validated['acct_start_time']);
            if ($ct !== null) {
                $session->connect_time = $ct;
            }
        }

        if ($status === 'stop') {
            $stopRaw = $validated['acct_stop_time'] ?? null;
            $session->disconnect_time = $this->parseFlexibleDateTime($stopRaw) ?? Carbon::now();

            if (($session->session_duration === null || (int) $session->session_duration === 0)
                && $session->connect_time !== null
                && $session->disconnect_time !== null) {
                $session->session_duration = (int) $session->connect_time->diffInSeconds($session->disconnect_time);
            }
        }

        if ($status === 'interim') {
            Log::info("Interim payloaddata:===============================================>>>>>>> ");
            Log::info($validated);
        }

        $session->last_update_time = Carbon::now();
        $session->save();

        return $session->fresh();
    }

    /**
     * @param  Carbon|string|numeric|null  $value
     */
    protected function parseFlexibleDateTime(mixed $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return Carbon::createFromTimestamp((int) $value, config('app.timezone'));
        }

        return Carbon::parse((string) $value);
    }
}
