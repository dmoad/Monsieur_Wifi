<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Organization;
use App\Models\User;
use App\Services\AuthzClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InternalController extends Controller
{
    /**
     * Claim a device for a user. Called by Nexus after validating an enrollment code.
     *
     * POST /api/internal/claim-device
     * {sub, email, name, mac_address}
     */
    public function claimDevice(Request $request)
    {
        $request->validate([
            'sub'         => 'required|string',
            'email'       => 'required|email',
            'name'        => 'required|string',
            'mac_address' => 'required|string',
        ]);

        $mac = strtoupper(str_replace(':', '-', $request->input('mac_address')));

        $device = Device::where('mac_address', $mac)
            ->orWhere('mac_address', str_replace('-', ':', $mac))
            ->first();

        if (! $device) {
            return response()->json([
                'error'   => 'device_not_found',
                'message' => 'No device with this MAC',
            ], 404);
        }

        if ($device->organization_id) {
            return response()->json([
                'error'   => 'already_claimed',
                'message' => 'Device already belongs to an organization',
            ], 409);
        }

        // Upsert user
        $user = User::updateOrCreate(
            ['email' => $request->input('email')],
            [
                'name'              => $request->input('name'),
                'zitadel_sub'       => $request->input('sub'),
                'email_verified_at' => now(),
                'password'          => bcrypt(str()->random(32)),
            ]
        );

        // Ensure org
        $isNewOrg = false;
        $org = null;

        if ($user->current_organization_id) {
            $org = Organization::find($user->current_organization_id);
        }

        if (! $org) {
            $org = Organization::where('owner_id', $user->id)->first();
        }

        if (! $org) {
            $org = Organization::create([
                'name'     => $request->input('name') ?: 'Mon organisation',
                'owner_id' => $user->id,
                'plan'     => 'free',
            ]);
            $isNewOrg = true;
        }

        // Set current org if not set
        if (! $user->current_organization_id || $user->current_organization_id !== $org->id) {
            $user->newQuery()->where('id', $user->id)->update(['current_organization_id' => $org->id]);
        }

        // Check plan limit
        $currentDevices = Device::where('organization_id', $org->id)->count();
        if (! $org->withinLimit('max_devices', $currentDevices)) {
            $limits = $org->getLimits();
            return response()->json([
                'error'   => 'limit_reached',
                'message' => 'Device limit reached',
                'limit'   => $limits['max_devices'] ?? 0,
                'plan'    => $org->plan,
            ], 402);
        }

        // Claim device
        $device->update([
            'owner_id'        => $user->id,
            'organization_id' => $org->id,
        ]);

        Log::info('Device claimed via internal API', [
            'device_id' => $device->id,
            'mac'       => $device->mac_address,
            'user_id'   => $user->id,
            'org_id'    => $org->id,
            'is_new_org'=> $isNewOrg,
        ]);

        return response()->json([
            'user_id'    => $user->id,
            'org_id'     => $org->id,
            'org_name'   => $org->name,
            'plan'       => $org->plan,
            'is_new_org' => $isNewOrg,
            'device'     => [
                'id'            => $device->id,
                'mac_address'   => $device->mac_address,
                'serial_number' => $device->serial_number,
                'name'          => $device->name,
            ],
        ]);
    }

    /**
     * Detach a device from its org. Called by Nexus before generating a transfer code.
     *
     * POST /api/internal/detach-device
     * {sub, device_id}
     */
    public function detachDevice(Request $request)
    {
        $request->validate([
            'sub'       => 'required|string',
            'device_id' => 'required|integer',
        ]);

        $device = Device::find($request->input('device_id'));
        if (! $device) {
            return response()->json(['error' => 'device_not_found'], 404);
        }

        $user = User::where('zitadel_sub', $request->input('sub'))->first();
        if (! $user) {
            return response()->json(['error' => 'not_owner', 'message' => 'User not found'], 403);
        }

        // Verify ownership: user owns the device or is in the same org
        $isOwner = $device->owner_id === $user->id;
        $isOrgMember = $device->organization_id && $device->organization_id === $user->current_organization_id;

        if (! $isOwner && ! $isOrgMember) {
            return response()->json(['error' => 'not_owner', 'message' => 'User does not own this device'], 403);
        }

        $macAddress = $device->mac_address;
        $serialNumber = $device->serial_number;

        DB::transaction(function () use ($device) {
            // Detach from location if assigned
            if ($device->location) {
                $device->location->update(['device_id' => null]);
            }

            // Detach device
            $device->update([
                'owner_id'        => null,
                'organization_id' => null,
            ]);
        });

        Log::info('Device detached via internal API', [
            'device_id' => $device->id,
            'mac'       => $macAddress,
            'user_id'   => $user->id,
        ]);

        return response()->json([
            'mac_address'   => $macAddress,
            'serial_number' => $serialNumber,
        ]);
    }

    /**
     * Get device info by MAC address.
     *
     * GET /api/internal/device-by-mac/{mac}
     */
    public function deviceByMac(string $mac)
    {
        $mac = strtoupper(str_replace(':', '-', $mac));

        $device = Device::where('mac_address', $mac)
            ->orWhere('mac_address', str_replace('-', ':', $mac))
            ->first();

        if (! $device) {
            return response()->json(['error' => 'device_not_found'], 404);
        }

        return response()->json([
            'id'              => $device->id,
            'mac_address'     => $device->mac_address,
            'serial_number'   => $device->serial_number,
            'organization_id' => $device->organization_id,
            'owner_id'        => $device->owner_id,
        ]);
    }
}
