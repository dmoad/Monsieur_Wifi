<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ZoneController extends Controller
{
    /**
     * Get all zones for the authenticated user (or all zones for admin/superadmin).
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (in_array($user->role, ['admin', 'superadmin'])) {
            // Admin can see all zones
            $zones = Zone::with(['owner', 'locations', 'primaryLocation'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Regular users see only their own zones
            $zones = Zone::with(['locations', 'primaryLocation'])
                ->where('owner_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return response()->json([
            'success' => true,
            'zones' => $zones
        ]);
    }

    /**
     * Create a new zone.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'owner_id' => 'nullable|exists:users,id', // For admin to create zones for other users
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Determine the owner_id
        $ownerId = $request->owner_id;
        
        // If not admin, force owner_id to be the current user
        if (!in_array($user->role, ['admin', 'superadmin'])) {
            $ownerId = $user->id;
        } else if (!$ownerId) {
            // If admin doesn't specify owner_id, use current user
            $ownerId = $user->id;
        }

        $zone = Zone::create([
            'name' => $request->name,
            'description' => $request->description,
            'owner_id' => $ownerId,
            'is_active' => true,
        ]);

        Log::info('Zone created', ['zone_id' => $zone->id, 'user_id' => $user->id]);

        return response()->json([
            'success' => true,
            'message' => 'Zone created successfully',
            'zone' => $zone->load('owner')
        ], 201);
    }

    /**
     * Get zone details with all locations.
     */
    public function show($id)
    {
        $user = Auth::user();
        $zone = Zone::with(['owner', 'locations.settings', 'primaryLocation.settings'])->find($id);

        if (!$zone) {
            return response()->json([
                'success' => false,
                'message' => 'Zone not found'
            ], 404);
        }

        // Check permission
        if ($zone->owner_id !== $user->id && !in_array($user->role, ['admin', 'superadmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'zone' => $zone
        ]);
    }

    /**
     * Update zone name and description.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $zone = Zone::find($id);

        if (!$zone) {
            return response()->json([
                'success' => false,
                'message' => 'Zone not found'
            ], 404);
        }

        // Check permission
        if ($zone->owner_id !== $user->id && !in_array($user->role, ['admin', 'superadmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $zone->update($request->only(['name', 'description', 'is_active']));

        Log::info('Zone updated', ['zone_id' => $zone->id, 'user_id' => $user->id]);

        return response()->json([
            'success' => true,
            'message' => 'Zone updated successfully',
            'zone' => $zone
        ]);
    }

    /**
     * Delete a zone (decouples all locations).
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $zone = Zone::with('locations')->find($id);

        if (!$zone) {
            return response()->json([
                'success' => false,
                'message' => 'Zone not found'
            ], 404);
        }

        // Check permission
        if ($zone->owner_id !== $user->id && !in_array($user->role, ['admin', 'superadmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Decouple all locations first
        foreach ($zone->locations as $location) {
            $location->zone_id = null;
            $location->save();
        }

        $zone->delete();

        Log::info('Zone deleted', ['zone_id' => $id, 'user_id' => $user->id]);

        return response()->json([
            'success' => true,
            'message' => 'Zone deleted successfully'
        ]);
    }

    /**
     * Add a location to a zone.
     */
    public function addLocation(Request $request, $zoneId, $locationId)
    {
        $user = Auth::user();
        $zone = Zone::find($zoneId);
        $location = Location::find($locationId);

        if (!$zone) {
            return response()->json([
                'success' => false,
                'message' => 'Zone not found'
            ], 404);
        }

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found'
            ], 404);
        }

        // Check permission
        if ($zone->owner_id !== $user->id && !in_array($user->role, ['admin', 'superadmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Validate that location belongs to the same owner as the zone
        if ($location->owner_id !== $zone->owner_id) {
            return response()->json([
                'success' => false,
                'message' => 'Location must belong to the same owner as the zone'
            ], 422);
        }

        // Check if location is already in another zone
        if ($location->zone_id && $location->zone_id != $zoneId) {
            return response()->json([
                'success' => false,
                'message' => 'Location is already in another zone'
            ], 422);
        }

        // Add location to zone
        $location->zone_id = $zoneId;
        $location->save();

        // If this is the first location in the zone, set it as primary
        if ($zone->locations()->count() === 1) {
            $zone->primary_location_id = $locationId;
            $zone->save();
        }

        Log::info('Location added to zone', [
            'zone_id' => $zoneId,
            'location_id' => $locationId,
            'user_id' => $user->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location added to zone successfully',
            'zone' => $zone->load(['locations', 'primaryLocation'])
        ]);
    }

    /**
     * Remove a location from a zone.
     */
    public function removeLocation(Request $request, $zoneId, $locationId)
    {
        $user = Auth::user();
        $zone = Zone::find($zoneId);
        $location = Location::find($locationId);

        if (!$zone) {
            return response()->json([
                'success' => false,
                'message' => 'Zone not found'
            ], 404);
        }

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found'
            ], 404);
        }

        // Check permission
        if ($zone->owner_id !== $user->id && !in_array($user->role, ['admin', 'superadmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Check if location is in this zone
        if ($location->zone_id != $zoneId) {
            return response()->json([
                'success' => false,
                'message' => 'Location is not in this zone'
            ], 422);
        }

        // If this location is primary and there are other locations, require new_primary_id
        $isPrimary = $zone->primary_location_id == $locationId;
        $remainingLocations = $zone->locations()->where('id', '!=', $locationId)->get();
        
        if ($isPrimary && $remainingLocations->count() > 0) {
            $newPrimaryId = $request->input('new_primary_id');
            
            if (!$newPrimaryId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a new primary location',
                    'requires_primary_selection' => true,
                    'remaining_locations' => $remainingLocations
                ], 422);
            }
            
            // Validate new primary is in the zone
            $newPrimary = Location::find($newPrimaryId);
            if (!$newPrimary || $newPrimary->zone_id != $zoneId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid new primary location'
                ], 422);
            }
            
            // Set new primary
            $zone->primary_location_id = $newPrimaryId;
        } else if ($isPrimary) {
            // No other locations, just clear primary
            $zone->primary_location_id = null;
        }
        
        $zone->save();

        // Remove location from zone
        $location->zone_id = null;
        $location->save();

        Log::info('Location removed from zone', [
            'zone_id' => $zoneId,
            'location_id' => $locationId,
            'user_id' => $user->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location removed from zone successfully',
            'zone' => $zone->load(['locations', 'primaryLocation'])
        ]);
    }

    /**
     * Set a location as the primary location for a zone.
     */
    public function setPrimaryLocation(Request $request, $zoneId, $locationId)
    {
        $user = Auth::user();
        $zone = Zone::find($zoneId);
        $location = Location::find($locationId);

        if (!$zone) {
            return response()->json([
                'success' => false,
                'message' => 'Zone not found'
            ], 404);
        }

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found'
            ], 404);
        }

        // Check permission
        if ($zone->owner_id !== $user->id && !in_array($user->role, ['admin', 'superadmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Validate that location is in this zone
        if ($location->zone_id != $zoneId) {
            return response()->json([
                'success' => false,
                'message' => 'Location must be in the zone to be set as primary'
            ], 422);
        }

        // Set as primary
        $zone->primary_location_id = $locationId;
        $zone->save();

        Log::info('Primary location set for zone', [
            'zone_id' => $zoneId,
            'location_id' => $locationId,
            'user_id' => $user->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Primary location set successfully',
            'zone' => $zone->load(['locations', 'primaryLocation'])
        ]);
    }

    /**
     * Get available locations (not in any zone) for a specific zone owner.
     */
    public function getAvailableLocations(Request $request, $zoneId)
    {
        $user = Auth::user();
        $zone = Zone::find($zoneId);

        if (!$zone) {
            return response()->json([
                'success' => false,
                'message' => 'Zone not found'
            ], 404);
        }

        // Check permission
        if ($zone->owner_id !== $user->id && !in_array($user->role, ['admin', 'superadmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Get locations owned by the zone's owner that are not in any zone
        $availableLocations = Location::where('owner_id', $zone->owner_id)
            ->whereNull('zone_id')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'locations' => $availableLocations
        ]);
    }
}
