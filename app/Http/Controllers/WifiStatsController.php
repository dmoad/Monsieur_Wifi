<?php

namespace App\Http\Controllers;

use App\Http\Requests\WifiStatsRequest;
use App\Models\Device;
use App\Models\GuestNetworkUser;
use App\Models\LocationNetwork;
use App\Models\WifiStat;
use App\Models\WifiStatClient;
use App\Services\UserDeviceLoginSessionStatsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WifiStatsController extends Controller
{
    /**
     * Ingest a wifi-stats payload sent by an AP every minute.
     *
     * Route: POST /api/devices/{device_key}/{device_secret}/network-stats
     */
    public function store(WifiStatsRequest $request, string $device_key, string $device_secret): JsonResponse
    {
        Log::info('WifiStatsController::store request: ');
        Log::info(json_encode($request->all()));
        // ── 1. Inline auth (same pattern as heartbeat / updateClientList) ──
        $device = Device::where('device_key', $device_key)
            ->where('device_secret', $device_secret)
            ->first();

        if (! $device) {
            return response()->json(['error' => 'Invalid device credentials'], 401);
        }

        $location = $device->location;
        $data     = $request->validated();

        // ── 2. Cross-check ap_id == device_key ─────────────────────────────
        if ($data['ap_id'] !== $device_key) {
            Log::warning('wifi-stats ap_id mismatch', [
                'ap_id'      => $data['ap_id'],
                'device_key' => $device_key,
                'payload'    => $data,
            ]);

            return response()->json(['error' => 'ap_id does not match device key'], 400);
        }

        // ── 3. Parse ts; flag bogus clocks, reject only if > 24h in future ─
        $apTs      = Carbon::parse($data['ts']);
        $tsFlagged = $apTs->year < 2020;

        if ($apTs->gt(now()->addHours(24))) {
            return response()->json(['error' => 'ts is too far in the future'], 400);
        }

        if ($tsFlagged) {
            Log::warning('wifi-stats bogus AP clock', [
                'ap_id' => $data['ap_id'],
                'ts'    => $data['ts'],
            ]);
        }

        // ── 4. Idempotency — skip duplicate (device_id, ap_ts) silently ────
        if (WifiStat::where('device_id', $device->id)->where('ap_ts', $apTs)->exists()) {
            return response()->json(['success' => true, 'duplicate' => true]);
        }

        // ── 5. Mapping verifications (log, never reject) ────────────────────
        $this->runVerifications($data);

        // ── 6. Zone-aware network map ────────────────────────────────────────
        // Zone members broadcast SSIDs defined on the primary location.
        // Mirror the same zone-coalescing logic used in getSettingsV2.
        $networksSourceId = $location?->id;

        if ($location?->zone_id && ! $location->isPrimaryInZone()) {
            $zone = $location->zone()->with('primaryLocation')->first();
            if ($zone?->primaryLocation) {
                $networksSourceId = $zone->primaryLocation->id;
            }
        }

        // ssid → location_networks.id, one query
        $networkMap = $networksSourceId
            ? LocationNetwork::where('location_id', $networksSourceId)->pluck('id', 'ssid')
            : collect();

        // ── 7. Build flat client rows ────────────────────────────────────────
        // GuestNetworkUser is resolved per slot in a single whereIn query
        // (not per-client) to avoid N+1 DB calls.
        $clientRows = [];

        /** @var UserDeviceLoginSessionStatsService $macVariantService */
        $macVariantService = app(UserDeviceLoginSessionStatsService::class);

        foreach ($data['slots'] as $slot) {
            $networkId = $networkMap[$slot['ssid']] ?? null;
            // JSON may send `"nasid": null` or `""`; store NULL when absent / empty.
            $nasidRaw = $slot['nasid'] ?? null;
            $nasidStored = ($nasidRaw !== null && $nasidRaw !== '') ? (string) $nasidRaw : null;
            $networkTypeRaw = $slot['network_type'] ?? null;
            $networkTypeStored = ($networkTypeRaw !== null && $networkTypeRaw !== '') ? (string) $networkTypeRaw : null;

            // Maps gnu rows (any legacy mac_address string) → wifi_stat_clients.mac storage form
            $canonicalMacByLookupKey = [];
            foreach ($slot['radios'] as $radio) {
                foreach ($radio['clients'] as $client) {
                    $canonical = WifiStatClient::storageFormatMac($client['mac']);
                    foreach ($macVariantService->macAddressVariants($canonical) as $variant) {
                        $canonicalMacByLookupKey[$variant] = $canonical;
                    }
                }
            }

            $gnuIdByCanonical = [];
            if ($networkId !== null && $canonicalMacByLookupKey !== []) {
                foreach (GuestNetworkUser::where('network_id', $networkId)
                    ->whereIn('mac_address', array_keys($canonicalMacByLookupKey))
                    ->get(['id', 'mac_address']) as $g) {
                    $canonicalRef = $canonicalMacByLookupKey[$g->mac_address] ?? null;
                    if ($canonicalRef !== null) {
                        $gnuIdByCanonical[$canonicalRef] = $g->id;
                    }
                }
            }

            foreach ($slot['radios'] as $radio) {
                foreach ($radio['clients'] as $client) {
                    $mac = WifiStatClient::storageFormatMac($client['mac']);

                    $clientRows[] = [
                        'ap_ts'                 => $apTs,
                        'mac'                   => $mac,
                        'location_id'           => $location?->id,
                        'zone_id'               => $location?->zone_id,
                        'location_network_id'   => $networkId,
                        'guest_network_user_id' => $gnuIdByCanonical[$mac] ?? null,
                        'slot'                  => $slot['slot'],
                        'ssid'                  => $slot['ssid'],
                        'nasid'                 => $nasidStored,
                        'network'               => $slot['network'],
                        'network_type'          => $networkTypeStored,
                        'radio'                 => $radio['radio'],
                        'iface'                 => $radio['iface'],
                        'band'                  => $radio['band'],
                        'ip'                    => $client['ip'] ?? null,
                        'signal_dbm'            => $client['signal_dbm'],
                        'signal_avg_dbm'        => $client['signal_avg_dbm'],
                        'snr_db'                => $client['snr_db'] ?? null,
                        'tx_retries'            => $client['tx_retries'],
                        'tx_failed'             => $client['tx_failed'],
                        'connected_time_s'      => $client['connected_time_s'],
                        'inactive_time_ms'      => $client['inactive_time_ms'],
                    ];
                }
            }
        }

        // ── 8. Persist atomically ────────────────────────────────────────────
        DB::transaction(function () use ($device, $data, $apTs, $tsFlagged, $clientRows) {
            $stat = WifiStat::create([
                'device_id'        => $device->id,
                'ap_id'            => $data['ap_id'],
                'ap_mac'           => $data['ap_mac'],
                'config_version'   => $data['config_version'],
                'firmware_version' => $data['firmware_version'],
                'ap_ts'            => $apTs,
                'ap_ts_flagged'    => $tsFlagged,
                'radios'           => $data['radios'],
                'received_at'      => now(),
            ]);

            // Bulk-insert client rows in chunks to avoid oversized queries
            foreach (array_chunk($clientRows, 200) as $chunk) {
                WifiStatClient::insert(
                    array_map(fn ($r) => array_merge($r, [
                        'wifi_stat_id' => $stat->id,
                        'ap_ts'        => $r['ap_ts']->toDateTimeString(),
                    ]), $chunk)
                );
            }
        });

        return response()->json(['success' => true], 200);
    }

    /**
     * Run the mapping verifications described in the spec.
     * Logs anomalies but never rejects the payload.
     */
    private function runVerifications(array $data): void
    {
        foreach ($data['slots'] as $slot) {
            $slotIndex = $slot['slot'];

            foreach ($slot['radios'] as $radio) {
                // iface derivation check: phy{N}-ap{slot}
                $phyN          = $radio['radio'] === 'radio0' ? 0 : 1;
                $expectedIface = "phy{$phyN}-ap{$slotIndex}";

                if ($radio['iface'] !== $expectedIface) {
                    Log::warning('wifi-stats iface mismatch', [
                        'ap_id'    => $data['ap_id'],
                        'slot'     => $slotIndex,
                        'radio'    => $radio['radio'],
                        'expected' => $expectedIface,
                        'actual'   => $radio['iface'],
                    ]);
                }

                // client_count vs actual clients length
                $actualCount = count($radio['clients']);
                if ($radio['client_count'] !== $actualCount) {
                    Log::warning('wifi-stats client_count mismatch', [
                        'ap_id'        => $data['ap_id'],
                        'slot'         => $slotIndex,
                        'radio'        => $radio['radio'],
                        'client_count' => $radio['client_count'],
                        'actual_count' => $actualCount,
                    ]);
                }

                // client.ssid must match slot.ssid
                foreach ($radio['clients'] as $client) {
                    if ($client['ssid'] !== $slot['ssid']) {
                        Log::error('wifi-stats client ssid mismatch — AP parser bug', [
                            'ap_id'       => $data['ap_id'],
                            'slot'        => $slotIndex,
                            'slot_ssid'   => $slot['ssid'],
                            'client_ssid' => $client['ssid'],
                            'client_mac'  => $client['mac'],
                        ]);
                    }
                }
            }
        }
    }
}
