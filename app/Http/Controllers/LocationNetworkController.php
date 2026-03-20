<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\LocationNetwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LocationNetworkController extends Controller
{
    private int $maxNetworks;

    public function __construct()
    {
        $this->maxNetworks = (int) env('MAX_NETWORKS_PER_LOCATION', 4);
    }

    // -------------------------------------------------------------------------
    // Authorization helper
    // -------------------------------------------------------------------------

    private function authorizeLocation(int $locationId): ?Location
    {
        $user = Auth::user();
        $isAdmin = in_array($user->role, ['admin', 'superadmin']);

        $location = $isAdmin
            ? Location::find($locationId)
            : Location::where('id', $locationId)->where(function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhereJsonContains('shared_users', ['user_id' => $user->id]);
            })->first();

        return $location;
    }

    // -------------------------------------------------------------------------
    // GET /api/v1/locations/{location_id}/networks
    // -------------------------------------------------------------------------

    public function index(int $locationId)
    {
        $location = $this->authorizeLocation($locationId);

        if (!$location) {
            return response()->json(['success' => false, 'message' => 'Location not found'], 404);
        }

        $networks = LocationNetwork::where('location_id', $locationId)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'networks'    => $networks,
                'max_networks' => $this->maxNetworks,
                'can_add'     => $networks->count() < $this->maxNetworks,
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /api/v1/locations/{location_id}/networks
    // -------------------------------------------------------------------------

    public function store(Request $request, int $locationId)
    {
        $location = $this->authorizeLocation($locationId);

        if (!$location) {
            return response()->json(['success' => false, 'message' => 'Location not found'], 404);
        }

        $currentCount = LocationNetwork::where('location_id', $locationId)->count();
        if ($currentCount >= $this->maxNetworks) {
            return response()->json([
                'success' => false,
                'message' => "Maximum of {$this->maxNetworks} networks per location reached.",
            ], 422);
        }

        $validated = $request->validate([
            'type'              => ['required', Rule::in(LocationNetwork::TYPES)],
            'ssid'              => 'required|string|max:32',
            'enabled'           => 'boolean',
            'visible'           => 'boolean',
            'vlan_id'           => 'nullable|integer|min:1|max:4094',
            'vlan_tagging'      => 'string',
            'password'          => 'nullable|string|min:8|max:63',
            'security'          => 'nullable|string',
            'cipher_suites'     => 'nullable|string',
            'auth_method'       => 'nullable|string',
            'portal_password'   => 'nullable|string',
            'social_auth_method'=> 'nullable|string',
            'session_timeout'   => 'nullable|integer|min:1',
            'idle_timeout'      => 'nullable|integer|min:1',
            'redirect_url'      => 'nullable|url',
            'portal_design_id'  => 'nullable|integer|exists:captive_portal_designs,id',
            'download_limit'    => 'nullable|integer|min:0',
            'upload_limit'      => 'nullable|integer|min:0',
            'working_hours'     => 'nullable|array',
            'working_hours.*.day'       => 'required_with:working_hours|string',
            'working_hours.*.startHour' => 'required_with:working_hours|integer|min:0|max:23',
            'working_hours.*.endHour'   => 'required_with:working_hours|integer|min:1|max:24',
            'ip_mode'           => 'nullable|string',
            'ip_address'        => 'nullable|ip',
            'netmask'           => 'nullable|string',
            'gateway'           => 'nullable|ip',
            'dns1'              => 'nullable|ip',
            'dns2'              => 'nullable|ip',
            'dhcp_enabled'      => 'boolean',
            'dhcp_start'        => 'nullable|ip',
            'dhcp_end'          => 'nullable|ip',
            'mac_filter_mode'   => 'nullable|string',
            'mac_filter_list'   => 'nullable|array',
            'qos_policy'        => 'nullable|string|in:full,scavenger',
        ]);

        // Set default password for password-type networks
        if ($validated['type'] === 'password' && empty($validated['password'])) {
            $validated['password'] = 'abcd1234';
        }

        // For Bridge to WAN mode, remove null IP/DHCP fields to avoid NOT NULL column errors
        if (($validated['ip_mode'] ?? null) === 'bridge') {
            foreach (['ip_address', 'netmask', 'gateway', 'dns1', 'dns2', 'dhcp_start', 'dhcp_end'] as $f) {
                unset($validated[$f]);
            }
            $validated['dhcp_enabled'] = false;
        }

        $sortOrder = $currentCount; // append at end

        $network = LocationNetwork::create(array_merge($validated, [
            'location_id' => $locationId,
            'sort_order'  => $sortOrder,
        ]));

        $this->incrementConfigVersion($location);

        return response()->json([
            'success' => true,
            'message' => 'Network created successfully.',
            'data'    => ['network' => $network],
        ], 201);
    }

    // -------------------------------------------------------------------------
    // GET /api/v1/locations/{location_id}/networks/{network_id}
    // -------------------------------------------------------------------------

    public function show(int $locationId, int $networkId)
    {
        $location = $this->authorizeLocation($locationId);

        if (!$location) {
            return response()->json(['success' => false, 'message' => 'Location not found'], 404);
        }

        $network = LocationNetwork::where('location_id', $locationId)->find($networkId);

        if (!$network) {
            return response()->json(['success' => false, 'message' => 'Network not found'], 404);
        }

        return response()->json(['success' => true, 'data' => ['network' => $network]]);
    }

    // -------------------------------------------------------------------------
    // PUT /api/v1/locations/{location_id}/networks/{network_id}
    // -------------------------------------------------------------------------

    public function update(Request $request, int $locationId, int $networkId)
    {
        $location = $this->authorizeLocation($locationId);
        # Throw and error for test purpose
        // return response()->json(['success' => false, 'message' => 'Network settings not updated'], 400);
        if (!$location) {
            return response()->json(['success' => false, 'message' => 'Location not found'], 404);
        }

        $network = LocationNetwork::where('location_id', $locationId)->find($networkId);

        if (!$network) {
            return response()->json(['success' => false, 'message' => 'Network not found'], 404);
        }

        $validated = $request->validate([
            'type'              => ['sometimes', Rule::in(LocationNetwork::TYPES)],
            'ssid'              => 'sometimes|string|max:32',
            'enabled'           => 'sometimes|boolean',
            'visible'           => 'sometimes|boolean',
            'vlan_id'           => 'nullable|integer|min:1|max:4094',
            'vlan_tagging'      => 'sometimes|string',
            'password'          => 'nullable|string|min:8|max:63',
            'security'          => 'nullable|string',
            'cipher_suites'     => 'nullable|string',
            'auth_method'       => 'nullable|string',
            'portal_password'   => 'nullable|string',
            'social_auth_method'=> 'nullable|string',
            'session_timeout'   => 'nullable|integer|min:1',
            'idle_timeout'      => 'nullable|integer|min:1',
            'redirect_url'      => 'nullable|url',
            'portal_design_id'  => 'nullable|integer|exists:captive_portal_designs,id',
            'download_limit'    => 'nullable|integer|min:0',
            'upload_limit'      => 'nullable|integer|min:0',
            'working_hours'     => 'nullable|array',
            'working_hours.*.day'       => 'required_with:working_hours|string',
            'working_hours.*.startHour' => 'required_with:working_hours|integer|min:0|max:23',
            'working_hours.*.endHour'   => 'required_with:working_hours|integer|min:1|max:24',
            'ip_mode'           => 'nullable|string',
            'ip_address'        => 'nullable|ip',
            'netmask'           => 'nullable|string',
            'gateway'           => 'nullable|ip',
            'dns1'              => 'nullable|ip',
            'dns2'              => 'nullable|ip',
            'dhcp_enabled'      => 'sometimes|boolean',
            'dhcp_start'        => 'nullable|ip',
            'dhcp_end'          => 'nullable|ip',
            'mac_filter_mode'   => 'nullable|string',
            'mac_filter_list'   => 'nullable|array',
            'qos_policy'        => 'nullable|string|in:full,scavenger',
        ]);

        // Check if updating to password type or if already password type
        $newType = $validated['type'] ?? $network->type;
        $isPasswordType = $newType === 'password';

        if ($isPasswordType) {
            $newPassword = $validated['password'] ?? null;

            if (array_key_exists('password', $validated) && empty($newPassword)) {
                // Caller explicitly sent an empty password — strip it from the update
                // so the existing DB value is preserved instead of being wiped.
                unset($validated['password']);
            }

            // After stripping, if there is still no password anywhere, reject
            if (!array_key_exists('password', $validated) && empty($network->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password is required for password-type networks.',
                    'errors' => [
                        'password' => ['Password is required for password-type networks.']
                    ]
                ], 422);
            }
        }

        // For Bridge to WAN mode, null out IP/DHCP fields so they are not written
        // (those columns may have NOT NULL constraints; bridge mode doesn't use them)
        if (($validated['ip_mode'] ?? $network->ip_mode) === 'bridge') {
            foreach (['ip_address', 'netmask', 'gateway', 'dns1', 'dns2', 'dhcp_start', 'dhcp_end'] as $f) {
                unset($validated[$f]);
            }
            $validated['dhcp_enabled'] = false;
        }

        // Determine if config version should be incremented
        $versionFields = [
            'type', 'ssid', 'enabled', 'visible', 'password', 'security',
            'auth_method', 'ip_address', 'netmask', 'gateway', 'dns1', 'dns2',
            'vlan_id', 'vlan_tagging', 'dhcp_enabled', 'dhcp_start', 'dhcp_end',
            'mac_filter_mode', 'mac_filter_list',
        ];

        $shouldIncrement = false;
        foreach ($versionFields as $field) {
            if (array_key_exists($field, $validated) && $validated[$field] != $network->$field) {
                $shouldIncrement = true;
                break;
            }
        }

        $network->fill($validated)->save();

        if ($shouldIncrement) {
            $this->incrementConfigVersion($location);
        }

        return response()->json([
            'success' => true,
            'message' => 'Network updated successfully.',
            'data'    => [
                'network'                  => $network->fresh(),
                'config_version_incremented' => $shouldIncrement,
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // DELETE /api/v1/locations/{location_id}/networks/{network_id}
    // -------------------------------------------------------------------------

    public function destroy(int $locationId, int $networkId)
    {
        $location = $this->authorizeLocation($locationId);

        if (!$location) {
            return response()->json(['success' => false, 'message' => 'Location not found'], 404);
        }

        $network = LocationNetwork::where('location_id', $locationId)->find($networkId);

        if (!$network) {
            return response()->json(['success' => false, 'message' => 'Network not found'], 404);
        }

        $deletedOrder = $network->sort_order;
        $network->delete();

        // Re-sequence sort_order for subsequent networks
        LocationNetwork::where('location_id', $locationId)
            ->where('sort_order', '>', $deletedOrder)
            ->orderBy('sort_order')
            ->each(function ($n) {
                $n->decrement('sort_order');
            });

        $this->incrementConfigVersion($location);

        return response()->json(['success' => true, 'message' => 'Network deleted.']);
    }

    // -------------------------------------------------------------------------
    // PUT /api/v1/locations/{location_id}/networks/reorder
    // Body: { "order": [id1, id2, id3, ...] }
    // -------------------------------------------------------------------------

    public function reorder(Request $request, int $locationId)
    {
        $location = $this->authorizeLocation($locationId);

        if (!$location) {
            return response()->json(['success' => false, 'message' => 'Location not found'], 404);
        }

        $request->validate([
            'order'   => 'required|array',
            'order.*' => 'integer',
        ]);

        foreach ($request->order as $index => $networkId) {
            LocationNetwork::where('location_id', $locationId)
                ->where('id', $networkId)
                ->update(['sort_order' => $index]);
        }

        $this->incrementConfigVersion($location);

        return response()->json(['success' => true, 'message' => 'Networks reordered.']);
    }

    // -------------------------------------------------------------------------
    // Private helper
    // -------------------------------------------------------------------------

    private function incrementConfigVersion(Location $location): void
    {
        $device = $location->device;
        if ($device) {
            $device->increment('configuration_version');
        }
    }
}
