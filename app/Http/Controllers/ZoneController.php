<?php

namespace App\Http\Controllers;

use App\Models\GuestNetworkUser;
use App\Models\Location;
use App\Models\UserDeviceLoginSession;
use App\Models\Zone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ZoneController extends Controller
{
    /**
     * Get all zones for the authenticated user (or all zones for admin/superadmin).
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->isAdminOrAbove()) {
            // Admin can see all zones
            $zones = Zone::with(['owner', 'locations', 'primaryLocation'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Regular users see their own zones and zones shared with them
            $zones = Zone::with(['locations', 'primaryLocation'])
                ->where(function ($q) use ($user) {
                    $q->where('owner_id', $user->id)
                        ->orWhereJsonContains('shared_users', ['user_id' => $user->id]);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return response()->json([
            'success' => true,
            'zones' => $zones,
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
            'roaming_enabled' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Determine the owner_id
        $ownerId = $request->owner_id;

        // If not admin, force owner_id to be the current user
        if (! $user->isAdminOrAbove()) {
            $ownerId = $user->id;
        } elseif (! $ownerId) {
            // If admin doesn't specify owner_id, use current user
            $ownerId = $user->id;
        }

        $zone = Zone::create([
            'name' => $request->name,
            'description' => $request->description,
            'owner_id' => $ownerId,
            'is_active' => true,
            'roaming_enabled' => $request->boolean('roaming_enabled', true),
        ]);

        Log::info('Zone created', ['zone_id' => $zone->id, 'user_id' => $user->id]);

        return response()->json([
            'success' => true,
            'message' => 'Zone created successfully',
            'zone' => $zone->load('owner'),
        ], 201);
    }

    /**
     * Get zone details with all locations.
     */
    public function show($id)
    {
        $user = Auth::user();
        $zone = Zone::with(['owner', 'locations.settings', 'primaryLocation.settings'])->find($id);

        if (! $zone) {
            return response()->json([
                'success' => false,
                'message' => 'Zone not found',
            ], 404);
        }

        // Check permission
        if (! $zone->isAccessibleBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'zone' => $zone,
        ]);
    }

    /**
     * Update zone name and description.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $zone = Zone::find($id);

        if (! $zone) {
            return response()->json([
                'success' => false,
                'message' => 'Zone not found',
            ], 404);
        }

        // Check permission
        if (! $zone->isAccessibleBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
            'roaming_enabled' => 'nullable|boolean',
        ];

        // Only admin/superadmin may manage shared_users and change owner
        if ($user->isAdminOrAbove()) {
            $rules['owner_id'] = 'sometimes|nullable|integer|exists:users,id';
            $rules['shared_users'] = 'sometimes|nullable|array';
            $rules['shared_users.*.user_id'] = 'required_with:shared_users|integer|exists:users,id';
            $rules['shared_users.*.access_level'] = 'required_with:shared_users|string|in:full,partial,read_only';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $roamingBefore = $zone->roaming_enabled;

        // Persist shared_users separately so JSON casting is applied correctly
        if ($user->isAdminOrAbove()) {
            if (array_key_exists('shared_users', $validated)) {
                $zone->shared_users = $validated['shared_users'];
                unset($validated['shared_users']);
            }
            if (! empty($validated['owner_id'])) {
                $zone->owner_id = $validated['owner_id'];
            }
            unset($validated['owner_id']);
        }

        $zone->update(array_intersect_key($validated, array_flip(['name', 'description', 'is_active', 'roaming_enabled'])));

        if (array_key_exists('roaming_enabled', $validated) && (bool) $roamingBefore !== (bool) $zone->roaming_enabled) {
            $zone->bumpConfigurationVersionForAllDevices();
        }

        Log::info('Zone updated', ['zone_id' => $zone->id, 'user_id' => $user->id]);

        return response()->json([
            'success' => true,
            'message' => 'Zone updated successfully',
            'zone' => $zone,
        ]);
    }

    /**
     * Delete a zone (decouples all locations).
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $zone = Zone::with('locations')->find($id);

        if (! $zone) {
            return response()->json([
                'success' => false,
                'message' => 'Zone not found',
            ], 404);
        }

        // Check permission
        if (! $zone->isAccessibleBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
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
            'message' => 'Zone deleted successfully',
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

        if (! $zone) {
            return response()->json([
                'success' => false,
                'message' => 'Zone not found',
            ], 404);
        }

        if (! $location) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found',
            ], 404);
        }

        // Check permission
        if (! $zone->isAccessibleBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Validate that location belongs to the same owner as the zone
        if ($location->owner_id !== $zone->owner_id) {
            return response()->json([
                'success' => false,
                'message' => 'Location must belong to the same owner as the zone',
            ], 422);
        }

        // Check if location is already in another zone
        if ($location->zone_id && $location->zone_id != $zoneId) {
            return response()->json([
                'success' => false,
                'message' => 'Location is already in another zone',
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
            'user_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location added to zone successfully',
            'zone' => $zone->load(['locations', 'primaryLocation']),
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

        if (! $zone) {
            return response()->json([
                'success' => false,
                'message' => 'Zone not found',
            ], 404);
        }

        if (! $location) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found',
            ], 404);
        }

        // Check permission
        if (! $zone->isAccessibleBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Check if location is in this zone
        if ($location->zone_id != $zoneId) {
            return response()->json([
                'success' => false,
                'message' => 'Location is not in this zone',
            ], 422);
        }

        // If this location is primary and there are other locations, require new_primary_id
        $isPrimary = $zone->primary_location_id == $locationId;
        $remainingLocations = $zone->locations()->where('id', '!=', $locationId)->get();

        if ($isPrimary && $remainingLocations->count() > 0) {
            $newPrimaryId = $request->input('new_primary_id');

            if (! $newPrimaryId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a new primary location',
                    'requires_primary_selection' => true,
                    'remaining_locations' => $remainingLocations,
                ], 422);
            }

            // Validate new primary is in the zone
            $newPrimary = Location::find($newPrimaryId);
            if (! $newPrimary || $newPrimary->zone_id != $zoneId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid new primary location',
                ], 422);
            }

            // Set new primary
            $zone->primary_location_id = $newPrimaryId;
        } elseif ($isPrimary) {
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
            'user_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location removed from zone successfully',
            'zone' => $zone->load(['locations', 'primaryLocation']),
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

        if (! $zone) {
            return response()->json([
                'success' => false,
                'message' => 'Zone not found',
            ], 404);
        }

        if (! $location) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found',
            ], 404);
        }

        // Check permission
        if (! $zone->isAccessibleBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Validate that location is in this zone
        if ($location->zone_id != $zoneId) {
            return response()->json([
                'success' => false,
                'message' => 'Location must be in the zone to be set as primary',
            ], 422);
        }

        // Set as primary
        $zone->primary_location_id = $locationId;
        $zone->save();

        Log::info('Primary location set for zone', [
            'zone_id' => $zoneId,
            'location_id' => $locationId,
            'user_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Primary location set successfully',
            'zone' => $zone->load(['locations', 'primaryLocation']),
        ]);
    }

    /**
     * Get available locations (not in any zone) for a specific zone owner.
     */
    public function getAvailableLocations(Request $request, $zoneId)
    {
        $user = Auth::user();
        $zone = Zone::find($zoneId);

        if (! $zone) {
            return response()->json([
                'success' => false,
                'message' => 'Zone not found',
            ], 404);
        }

        // Check permission
        if (! $zone->isAccessibleBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Get locations owned by the zone's owner that are not in any zone
        $availableLocations = Location::where('owner_id', $zone->owner_id)
            ->whereNull('zone_id')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'locations' => $availableLocations,
        ]);
    }

    // =========================================================================
    // ANALYTICS ENDPOINTS
    // =========================================================================

    /**
     * Hourly bandwidth for the zone over the last 24 h.
     * Returns 24 buckets: { hour, download, upload } in byte totals.
     */
    public function getAnalyticsHourlyBandwidth($zoneId)
    {
        try {
            $zone = Zone::find($zoneId);
            if (! $zone) {
                return response()->json(['success' => false, 'message' => 'Zone not found'], 404);
            }

            $offsetMinutes = (int) round(Carbon::now()->utcOffset());
            $hourExpr = $offsetMinutes === 0
                ? 'HOUR(connect_time)'
                : "HOUR(DATE_ADD(connect_time, INTERVAL {$offsetMinutes} MINUTE))";

            $since = Carbon::now()->subHours(24);

            $rows = UserDeviceLoginSession::query()
                ->selectRaw("{$hourExpr} AS hr,
                             SUM(COALESCE(total_download, 0)) AS dl,
                             SUM(COALESCE(total_upload, 0))   AS ul")
                ->where('zone_id', $zoneId)
                ->where('login_success', true)
                ->where('connect_time', '>=', $since)
                ->groupByRaw($hourExpr)
                ->orderByRaw($hourExpr)
                ->get()
                ->keyBy('hr');

            $buckets = [];
            for ($h = 0; $h < 24; $h++) {
                $row = $rows->get($h);
                $buckets[] = [
                    'hour' => sprintf('%02d:00', $h),
                    'download' => $row ? (int) $row->dl : 0,
                    'upload' => $row ? (int) $row->ul : 0,
                ];
            }

            return response()->json(['success' => true, 'data' => $buckets]);
        } catch (\Exception $e) {
            Log::error('Zone analytics hourly bandwidth error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Per-day aggregates for the zone (same shape as LocationController::getCaptivePortalDailyUsage).
     * Query param: period (today | 7days | 30days | 90days | numeric).
     */
    public function getAnalyticsDailyUsage($zoneId, Request $request)
    {
        try {
            $zone = Zone::find($zoneId);
            if (! $zone) {
                return response()->json(['success' => false, 'message' => 'Zone not found'], 404);
            }

            $periodRaw = $request->get('period', '7days');
            if ($periodRaw === 'today') {
                $startDate = Carbon::today()->startOfDay();
                $endDate = Carbon::today()->endOfDay();
                $days = 1;
            } else {
                $days = (int) preg_replace('/[^0-9]/', '', (string) $periodRaw);
                if ($days < 1) {
                    $days = 7;
                }
                $endDate = Carbon::today()->endOfDay();
                $startDate = Carbon::today()->subDays($days - 1)->startOfDay();
            }

            $offsetMinutes = (int) round(Carbon::now()->utcOffset());
            $dateExpr = $offsetMinutes === 0
                ? 'DATE(connect_time)'
                : "DATE(DATE_ADD(connect_time, INTERVAL {$offsetMinutes} MINUTE))";

            $rows = UserDeviceLoginSession::query()
                ->selectRaw("
                    {$dateExpr}                              AS day,
                    COUNT(*)                                 AS sessions,
                    COUNT(DISTINCT guest_network_user_id)    AS unique_users,
                    SUM(COALESCE(total_download, 0))         AS total_download,
                    SUM(COALESCE(total_upload, 0))           AS total_upload,
                    SUM(CASE WHEN session_duration IS NOT NULL THEN session_duration ELSE 0 END) AS total_duration,
                    SUM(CASE WHEN session_duration IS NOT NULL THEN 1 ELSE 0 END)               AS sessions_with_duration
                ")
                ->where('zone_id', $zoneId)
                ->where('login_success', true)
                ->whereBetween('connect_time', [$startDate, $endDate])
                ->groupByRaw($dateExpr)
                ->orderByRaw($dateExpr)
                ->get()
                ->keyBy('day');

            $dailyStats = [];
            $cursor = $startDate->copy()->startOfDay();
            while ($cursor->lte($endDate)) {
                $key = $cursor->format('Y-m-d');
                $row = $rows->get($key);
                $dailyStats[] = [
                    'date' => $cursor->format('M j'),
                    'unique_users' => $row ? (int) $row->unique_users : 0,
                    'sessions' => $row ? (int) $row->sessions : 0,
                    'total_download' => $row ? (int) $row->total_download : 0,
                    'total_upload' => $row ? (int) $row->total_upload : 0,
                ];
                $cursor->addDay();
            }

            $period = UserDeviceLoginSession::query()
                ->selectRaw('
                    COUNT(*)                                 AS total_sessions,
                    COUNT(DISTINCT guest_network_user_id)    AS unique_users,
                    SUM(COALESCE(total_download, 0))         AS total_download,
                    SUM(COALESCE(total_upload, 0))           AS total_upload,
                    SUM(CASE WHEN session_duration IS NOT NULL THEN session_duration ELSE 0 END) AS total_duration,
                    SUM(CASE WHEN session_duration IS NOT NULL THEN 1 ELSE 0 END)               AS sessions_with_duration
                ')
                ->where('zone_id', $zoneId)
                ->where('login_success', true)
                ->whereBetween('connect_time', [$startDate, $endDate])
                ->first();

            $totalSessions = (int) ($period->total_sessions ?? 0);
            $uniqueUsers = (int) ($period->unique_users ?? 0);
            $totalDownload = (int) ($period->total_download ?? 0);
            $totalUpload = (int) ($period->total_upload ?? 0);
            $withDuration = (int) ($period->sessions_with_duration ?? 0);
            $totalDuration = (int) ($period->total_duration ?? 0);
            $avgSessionSecs = $withDuration > 0 ? (int) round($totalDuration / $withDuration) : 0;
            $avgDailyUsers = $days > 0 ? round($uniqueUsers / $days, 1) : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'total_download' => $totalDownload,
                    'total_upload' => $totalUpload,
                    'unique_users' => $uniqueUsers,
                    'total_sessions' => $totalSessions,
                    'avg_session_seconds' => $avgSessionSecs,
                    'avg_daily_users' => $avgDailyUsers,
                    'daily_stats' => $dailyStats,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Zone analytics daily usage error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Device-type breakdown for the zone, deduplicated by mac_address.
     * Returns [{ type, count }] sorted descending.
     */
    public function getAnalyticsDeviceTypes($zoneId)
    {
        try {
            $zone = Zone::find($zoneId);
            if (! $zone) {
                return response()->json(['success' => false, 'message' => 'Zone not found'], 404);
            }

            // One row per unique MAC (latest by id) before grouping by device_type.
            $dedupIds = GuestNetworkUser::selectRaw('MAX(id) as id')
                ->where('zone_id', $zoneId)
                ->groupBy('mac_address');

            $rows = GuestNetworkUser::joinSub($dedupIds, 'd', 'guest_network_users.id', '=', 'd.id')
                ->selectRaw("COALESCE(NULLIF(TRIM(device_type), ''), 'Unknown') AS dtype, COUNT(*) AS cnt")
                ->groupByRaw("COALESCE(NULLIF(TRIM(device_type), ''), 'Unknown')")
                ->orderByRaw('cnt DESC')
                ->get();

            $data = $rows->map(fn ($r) => ['type' => $r->dtype, 'count' => (int) $r->cnt])->values();

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            Log::error('Zone analytics device types error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Paginated unique guest-user list for the zone (deduplicated by mac_address).
     * Query params: page, per_page (max 100), search (name/mac/email).
     */
    public function getAnalyticsUsers($zoneId, Request $request)
    {
        try {
            $zone = Zone::find($zoneId);
            if (! $zone) {
                return response()->json(['success' => false, 'message' => 'Zone not found'], 404);
            }

            $perPage = min((int) $request->get('per_page', 15), 100);
            $search = trim((string) $request->get('search', ''));

            // Dedup subquery: keep the most recent record (MAX id) per mac_address.
            $dedupIds = GuestNetworkUser::selectRaw('MAX(id) as id')
                ->where('zone_id', $zoneId)
                ->groupBy('mac_address');

            if ($search !== '') {
                $like = '%'.$search.'%';
                $dedupIds->where(function ($q) use ($like) {
                    $q->where('name', 'like', $like)
                        ->orWhere('mac_address', 'like', $like)
                        ->orWhere('email', 'like', $like);
                });
            }

            $query = GuestNetworkUser::joinSub($dedupIds, 'd', 'guest_network_users.id', '=', 'd.id')
                ->select('guest_network_users.*')
                ->selectSub(
                    UserDeviceLoginSession::query()
                        ->selectRaw('COUNT(*)')
                        ->whereColumn('guest_network_user_id', 'guest_network_users.id'),
                    'session_count'
                )
                ->selectSub(
                    UserDeviceLoginSession::query()
                        ->selectRaw('MAX(connect_time)')
                        ->whereColumn('guest_network_user_id', 'guest_network_users.id'),
                    'last_seen'
                )
                ->orderByDesc('guest_network_users.id');

            $paginated = $query->paginate($perPage);

            $items = collect($paginated->items())->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'mac_address' => $u->mac_address,
                'email' => $u->email,
                'phone' => $u->phone,
                'os' => $u->os,
                'device_type' => $u->device_type,
                'blocked' => (bool) $u->blocked,
                'expiration_time' => $u->expiration_time?->toDateTimeString(),
                'session_count' => (int) $u->session_count,
                'last_seen' => $u->last_seen,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $items,
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                    'total' => $paginated->total(),
                    'per_page' => $paginated->perPage(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Zone analytics users error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
