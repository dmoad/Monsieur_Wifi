<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Device;
use App\Models\LocationSettingsV2;
use App\Models\SystemSetting;
use App\Models\Radacct;
use App\Models\Radcheck;
use App\Models\Firmware;
use App\Models\CaptivePortalWorkingHour;
use App\Models\OnlineNetworkUser;
use App\Models\Category;
use App\Services\GeocodingService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
class LocationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // For web routes only, API routes already have middleware defined
        if (request()->is('api/*')) {
            // Don't apply middleware for API routes
        } else {
            $this->middleware('auth');
            $this->middleware('role:admin');
        }
    }

   
    /**
     * Display a listing of the locations.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if (in_array($user->role, ['admin', 'superadmin'])) {
            // Get locations with their associated devices and zones
            $locations = Location::with(['device', 'zone'])->get();
        } else {
            $locations = Location::with(['device', 'zone'])->where(function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhereJsonContains('shared_users', ['user_id' => $user->id]);
            })->get();
        }
        
        // Determine online status for each location's device
        $locationsWithStatus = $locations->map(function ($location) {
            // Create a new array with location data
            $locationData = $location->toArray();
            
            // Default status is offline
            $locationData['online_status'] = 'offline';
            
            // Check if device exists and has last_seen timestamp
            if ($location->device && $location->device->last_seen) {
                $lastSeen = new \DateTime($location->device->last_seen);
                $now = new \DateTime();
                $interval = $now->getTimestamp() - $lastSeen->getTimestamp();
                
                // If last_seen is within 90 seconds, device is online
                if ($interval <= 90) {
                    $locationData['online_status'] = 'online';
                }
                
                // Add last_seen timestamp to the response
                $locationData['last_seen'] = $location->device->last_seen;
            }
            // Get data usage and user statistics from accounting records for today
            $today = \Carbon\Carbon::now()->startOfDay();
            $dataUsage = Radacct::getLocationDataUsage($location->id, $today);

            $activeSessions = Radacct::getActiveSessions($location->id);
            
            $locationData['users'] = $activeSessions->count();
            $locationData['unique_users_today'] = $dataUsage['unique_users'];
            $locationData['data_usage'] = $dataUsage['total_mb']; // In MB
            $locationData['data_usage_gb'] = $dataUsage['total_gb']; // In GB
            $locationData['total_sessions'] = $dataUsage['total_sessions'];
            $locationData['active_sessions'] = $activeSessions->count();
            $locationData['settings'] = LocationSettingsV2::where('location_id', $location->id)->first();
            return $locationData;
        });

        // Calculate network-wide totals using the Radacct model helper methods
        $networkTotals = [
            'total_input_bytes' => 0,
            'total_output_bytes' => 0,
            'total_users' => 0,
            'total_data_gb' => 0
        ];
        
        // Sum up actual data usage across all locations using Radacct model methods
        foreach ($locations as $location) {
            // Get all-time data usage for each location
            $allTimeUsage = Radacct::getLocationDataUsage($location->id);

            $networkTotals['total_input_bytes'] += $allTimeUsage['total_input_bytes'];
            $networkTotals['total_output_bytes'] += $allTimeUsage['total_output_bytes'];
            $networkTotals['total_users'] += $allTimeUsage['unique_users'];
            $networkTotals['total_data_gb'] += $allTimeUsage['total_gb'];
        }

        return response()->json([
            'success' => true,
            'message' => 'Locations fetched successfully',
            'locations' => $locationsWithStatus,
            'network_totals' => $networkTotals
        ]);
    }

    /**
     * Show the form for creating a new location.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('locations.create');
    }

    /**
     * Store a newly created location in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info('Store location request received');
        Log::info($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'description' => 'nullable|string',
            'manager_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'device_id' => 'required|exists:devices,id',
            'owner_id' => 'nullable|exists:users,id',
        ]);

        // Geocode address if provided and lat/lng not already set
        if (!$request->latitude && !$request->longitude && 
            ($request->address || $request->city || $request->state || $request->country || $request->postal_code)) {
            
            Log::info('Attempting to geocode address for new location');
            $geocodingService = new GeocodingService();
            $geocodeResult = $geocodingService->geocodeAddress(
                $request->address,
                $request->city,
                $request->state,
                $request->country,
                $request->postal_code
            );
            
            if ($geocodeResult) {
                $request->merge([
                    'latitude' => $geocodeResult['lat'],
                    'longitude' => $geocodeResult['lng']
                ]);
                Log::info('Successfully geocoded address', [
                    'latitude' => $geocodeResult['lat'],
                    'longitude' => $geocodeResult['lng'],
                    'formatted_address' => $geocodeResult['formatted_address']
                ]);
            } else {
                Log::warning('Failed to geocode address for new location');
            }
        }

        // Get existing device with firmware relationship
        $device = Device::with('firmware')->find($request->device_id);
        
        // Check if device is already assigned to another location
        $existingLocation = Location::where('device_id', $device->id)->first();
        if ($existingLocation) {
            // Device is assigned to another location - reassign it
            Log::info('Reassigning device to new location', [
                'device_id' => $device->id,
                'old_location' => $existingLocation->name,
                'old_location_id' => $existingLocation->id,
                'new_location_request' => $request->name
            ]);
            
            // Remove device from old location
            $existingLocation->device_id = null;
            $existingLocation->save();
            
            Log::info('Device removed from old location', [
                'old_location_id' => $existingLocation->id,
                'old_location_name' => $existingLocation->name
            ]);
        }
        
        // Get the firmware from the device
        $firmware = $device->firmware;

        // Determine the owner_id
        $user = Auth::user();
        $ownerId = $request->owner_id;
        
        // If not admin, force owner_id to be the current user
        if (!in_array($user->role, ['admin', 'superadmin'])) {
            $ownerId = $user->id;
        } else if (!$ownerId) {
            // If admin doesn't specify owner_id, use current user
            $ownerId = $user->id;
        }

        // Create the location with the device
        $location = new Location($request->except(['mac_address', 'device_name', 'serial_number', 'owner_id']));
        $location->device_id = $device->id;
        $location->user_id = $ownerId;  // User who created/manages the location
        $location->owner_id = $ownerId; // Owner of the location
        $location->save();

        // Create the location settings (v2 — router-level only)
        $locationSettings = new LocationSettingsV2();
        $locationSettings->location_id = $location->id;
        $locationSettings->save();

        // create the working hours for whole week with all day active 

        $working_hours = $this->createBusinessWorkingHours($location->id);

        // Return JSON response
        return response()->json([
            'success' => true,
            'message' => 'Location and device created successfully.',
            'location' => $location,
            'device' => $device,
            'firmware' => $firmware ? [
                'id' => $firmware->id,
                'name' => $firmware->name,
                'version' => $firmware->version,
                'is_default' => $firmware->default_model_firmware
            ] : null
        ]);
    }

    /**
     * Clone an existing location (with its settings and networks) to a new owner.
     */
    public function clone(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        // Load source location with settings and networks
        $source = Location::with(['settings', 'networks'])->find($id);

        if (!$source) {
            return response()->json(['success' => false, 'message' => 'Location not found'], 404);
        }

        // Only allow cloning own location (or any location for admins)
        if (!in_array($user->role, ['admin', 'superadmin']) && $source->owner_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Determine owner for the cloned location
        $ownerId = $user->id;
        if (in_array($user->role, ['admin', 'superadmin']) && $request->filled('owner_id')) {
            $request->validate(['owner_id' => 'exists:users,id']);
            $ownerId = $request->owner_id;
        }

        // Create the cloned location (no device_id — must be assigned separately)
        $cloned = new Location($source->only([
            'address', 'city', 'state', 'country', 'postal_code',
            'latitude', 'longitude', 'description',
            'manager_name', 'contact_email', 'contact_phone', 'status',
        ]));
        $cloned->name      = $source->name . ' (Copy)';
        $cloned->user_id   = $ownerId;
        $cloned->owner_id  = $ownerId;
        $cloned->device_id = null;
        $cloned->save();

        // Clone LocationSettingsV2
        if ($source->settings) {
            $settingsData = $source->settings->toArray();
            unset($settingsData['id'], $settingsData['location_id'], $settingsData['created_at'], $settingsData['updated_at']);
            $clonedSettings = new LocationSettingsV2($settingsData);
            $clonedSettings->location_id = $cloned->id;
            $clonedSettings->save();
        } else {
            $clonedSettings = new LocationSettingsV2(['location_id' => $cloned->id]);
            $clonedSettings->save();
        }

        // Clone all networks
        foreach ($source->networks as $network) {
            $networkData = $network->toArray();
            unset($networkData['id'], $networkData['location_id'], $networkData['created_at'], $networkData['updated_at']);
            $clonedNetwork = new \App\Models\LocationNetwork($networkData);
            $clonedNetwork->location_id = $cloned->id;
            $clonedNetwork->save();
        }

        return response()->json([
            'success'  => true,
            'message'  => 'Location cloned successfully.',
            'location' => $cloned->load('settings'),
        ]);
    }

    /**
     * Display the specified location.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        if (in_array($user->role, ['admin', 'superadmin'])) {
            $location = Location::with(['device', 'zone', 'settings'])->find($id);
        } else {
            $location = Location::with(['device', 'zone', 'settings'])->where(function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhereJsonContains('shared_users', ['user_id' => $user->id]);
            })->find($id);
        }
        
        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 200);
        }
        
        $device = Device::with('productModel')->find($location->device_id);
        $locationSettings = LocationSettingsV2::where('location_id', $id)->first();

        $locationData = $location->toArray();
        $locationData['settings'] = $locationSettings;
        $locationData['device'] = $device;
        
        // Add zone-related information
        $locationData['is_primary_in_zone'] = $location->isPrimaryInZone();
        $locationData['can_edit_settings'] = $location->canEditSettings();

        // For non-primary zone members, expose the primary location id and access flag
        if ($location->zone_id && !$location->isPrimaryInZone()) {
            $zone = $location->zone()->with('primaryLocation')->first();
            $primaryLocation = $zone ? $zone->primaryLocation : null;
            $locationData['primary_location_id'] = $primaryLocation ? $primaryLocation->id : null;
            $locationData['can_access_primary'] = $primaryLocation
                ? $primaryLocation->isAccessibleBy($user)
                : false;
        } else {
            $locationData['primary_location_id'] = null;
            $locationData['can_access_primary'] = true;
        }
        
        if ($device) {
            $device->is_online = false;
            // If device last_seen is older than 90 seconds, set device online to false
            if ($device->last_seen) {
                $now = new \DateTime();
                $lastSeen = new \DateTime($device->last_seen);
                $interval = $now->getTimestamp() - $lastSeen->getTimestamp();
                if ($interval <= 90) {
                    $device->is_online = true;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Location fetched successfully',
            'data' => $locationData
        ]);
    }

    /**
     * Get accounting statistics for a location
     */
    public function getAccounting($id, Request $request)
    {
        try {
            $location = Location::find($id);
            
            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found'
                ], 404);
            }
            
            // Get date range from request, default to last 30 days
            $startDate = $request->has('start_date') 
                ? \Carbon\Carbon::parse($request->start_date) 
                : \Carbon\Carbon::now()->subDays(30);
            $endDate = $request->has('end_date') 
                ? \Carbon\Carbon::parse($request->end_date) 
                : \Carbon\Carbon::now();
            
            // Get comprehensive statistics
            $dataUsage = Radacct::getLocationDataUsage($id, $startDate, $endDate);
            Log::info('Data usage: ');
            Log::info($dataUsage);
            $activeSessions = Radacct::getActiveSessions($id);
            $recentSessions = Radacct::getRecentSessions($id, 20);
            $dailyStats = Radacct::getSessionStats($id, $startDate, $endDate);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'location' => $location,
                    'date_range' => [
                        'start' => $startDate->format('Y-m-d'),
                        'end' => $endDate->format('Y-m-d')
                    ],
                    'summary' => $dataUsage,
                    'active_sessions' => $activeSessions,
                    'recent_sessions' => $recentSessions,
                    'daily_statistics' => $dailyStats
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting location accounting data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving accounting data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user session history for a location
     */
    public function getUserSessions($id, Request $request)
    {
        try {
            $location = Location::find($id);
            
            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found'
                ], 404);
            }
            
            $username = $request->input('username');
            if (!$username) {
                return response()->json([
                    'success' => false,
                    'message' => 'Username is required'
                ], 400);
            }
            
            // Get user sessions for this location
            $sessions = Radacct::getByUsernameAndLocation($username, $id);
            $userDataUsage = Radacct::getUserDataUsage($username, $id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'location' => $location,
                    'username' => $username,
                    'sessions' => $sessions,
                    'usage_summary' => $userDataUsage
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting user sessions: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving user sessions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get online users for a specific location
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getOnlineUsers($id)
    {
        try {
            $location = Location::find($id);
            
            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found'
                ], 404);
            }
            
            // Get all online users for this location
            $onlineUsers = OnlineNetworkUser::where('location_id', $id)
                ->orderBy('updated_at', 'desc')
                ->get();
            
            // Transform the data to include additional information
            $transformedUsers = $onlineUsers->map(function ($user) {
                return [
                    'id' => $user->id,
                    'mac' => $user->mac,
                    'type' => $user->type,
                    'ip' => $user->ip,
                    'interface' => $user->interface,
                    'hostname' => $user->hostname ?: 'Unknown Device',
                    'network' => $user->network,
                    'connected_time' => $user->updated_at->diffForHumans(),
                    'last_seen' => $user->updated_at->format('Y-m-d H:i:s'),
                    'network_badge' => $user->network === 'captive' ? 'badge-light-info' : 'badge-light-success',
                    'network_label' => $user->network === 'captive' ? 'Captive Portal' : 'Password WiFi'
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => [
                    'location' => $location,
                    'online_users' => $transformedUsers,
                    'total_count' => $onlineUsers->count(),
                    'captive_count' => $onlineUsers->where('network', 'captive')->count(),
                    'password_count' => $onlineUsers->where('network', 'lan')->count()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting online users: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving online users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get captive portal daily usage statistics from Radacct
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCaptivePortalDailyUsage($id, Request $request)
    {
        try {
            $location = Location::find($id);

            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found'
                ], 404);
            }

            // Get period from request, default to 7 days
            $period = (int) $request->get('period', 7);

            // Calculate date range - ensure we include today's data
            $endDate = Carbon::today()->endOfDay();
            $startDate = Carbon::today()->subDays($period - 1)->startOfDay();

            // Add debug info
            Log::info("Date range calculation - Period: {$period} days");
            Log::info("Start date: {$startDate->format('Y-m-d H:i:s')}");
            Log::info("End date: {$endDate->format('Y-m-d H:i:s')}");
            Log::info("Today: " . Carbon::today()->format('Y-m-d'));

            // Get daily statistics from Radacct
            Log::info("Getting captive portal daily usage for location {$id}: {$startDate->format('Y-m-d H:i:s')} to {$endDate->format('Y-m-d H:i:s')}");
            $dailyStats = Radacct::getSessionStats($id, $startDate, $endDate);
            Log::info("Retrieved daily stats count: " . count($dailyStats));

            // Transform the data for the chart
            $chartData = [];
            Log::info("Processing daily stats for chart data:");
            foreach ($dailyStats as $date => $stats) {
                Log::info("Date: {$date}, Users: {$stats['unique_users']}, Sessions: {$stats['sessions']}");
                $chartData[] = [
                    'date' => Carbon::parse($date)->format('M j'), // Format as "Jan 15"
                    'users' => $stats['unique_users'],
                    'sessions' => $stats['sessions'],
                    'data_usage' => $stats['total_bytes'],
                    'session_time' => $stats['total_session_time']
                ];
            }
            
            // Calculate summary statistics
            $totalUsers = collect($dailyStats)->sum('unique_users');
            $totalSessions = collect($dailyStats)->sum('sessions');
            $totalDataUsage = collect($dailyStats)->sum('total_bytes');
            $averageUsersPerDay = $totalUsers > 0 ? round($totalUsers / $period, 1) : 0;
            
            return response()->json([
                'success' => true,
                'data' => $chartData,
                'summary' => [
                    'total_unique_users' => $totalUsers,
                    'total_sessions' => $totalSessions,
                    'total_data_usage' => $totalDataUsage,
                    'average_users_per_day' => $averageUsersPerDay,
                    'period_days' => $period,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d')
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting captive portal daily usage: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving captive portal usage data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified location.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }

    /**
     * Update the specified location in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $location_id)
    {
        Log::info('Update location request received');
        Log::info($request->all());
        

        // Check if it's a settings update
        if ($request->has('settings') && $request->has('settings_type')) {
            $settingsType = $request->input('settings_type');
            $settings = $request->input('settings');
            $location = Location::find($location_id);
            $increment_version = 0;
            $device = Device::find($location->device_id);
            Log::info('settings: ');
            Log::info($settings);
            Log::info('settingsType: ' . $settingsType);
            
            try {
                // Find the location settings
                $locationSettings = LocationSettingsV2::where('location_id', $location_id)->first();
                
                // If settings don't exist, create them
                if (!$locationSettings) {
                    $locationSettings = new LocationSettingsV2();
                    $locationSettings->location_id = $location_id;
                }

                Log::info("settingsType :: ");
                Log::info($settingsType);

                // Handle different types of settings
                if ($settingsType === 'captive' || $settingsType === 'captive_portal') {
                    Log::info('=== CAPTIVE PORTAL SETTINGS UPDATE ===');
                    Log::info('Settings type: ' . $settingsType);
                    Log::info('Location ID: ' . $location_id);
                    Log::info('Settings data captive: ' . json_encode($settings));
                    
                    // Ensure working hours exist for this location when updating captive portal settings
                    $this->createBusinessWorkingHours($location_id);
                    
                    // Update captive portal settings
                    if (isset($settings['captive_portal_ssid'])) {
                        if ($settings['captive_portal_ssid'] !== $locationSettings->captive_portal_ssid) {
                            $increment_version = 1;
                            Log::info('Captive portal ssid updated');
                        }
                        $locationSettings->captive_portal_ssid = $settings['captive_portal_ssid'];
                    }
                    
                    if (isset($settings['captive_portal_visible'])) {
                        // Convert both values to boolean for proper comparison
                        $newValue = (bool)$settings['captive_portal_visible'];
                        $oldValue = (bool)$locationSettings->captive_portal_visible;
                        
                        if ($newValue !== $oldValue) {
                            $increment_version = 1;
                            Log::info('Captive portal visible updated');
                            Log::info('New value: ' . ($newValue ? 1 : 0));
                            Log::info('Old value: ' . ($oldValue ? 1 : 0));
                        }
                        $locationSettings->captive_portal_visible = $newValue;
                        Log::info('Captive portal visible updated to: ' . ($newValue ? 1 : 0));
                    }
                    
                    if (isset($settings['captive_portal_enabled'])) {
                        if ($settings['captive_portal_enabled'] !== $locationSettings->captive_portal_enabled) {
                            $increment_version = 1;
                            Log::info('Captive portal enabled updated');
                        }
                        $locationSettings->captive_portal_enabled = $settings['captive_portal_enabled'];
                    }

                    if (isset($settings['captive_portal_ip'])) {
                        if ($settings['captive_portal_ip'] !== $locationSettings->captive_portal_ip) {
                            $increment_version = 1;
                            Log::info('Captive portal ip updated');
                        }
                        $locationSettings->captive_portal_ip = $settings['captive_portal_ip'];
                    }

                    if (isset($settings['captive_portal_netmask'])) {
                        if ($settings['captive_portal_netmask'] !== $locationSettings->captive_portal_netmask) {
                            $increment_version = 1;
                            Log::info('Captive portal netmask updated');
                        }
                        $locationSettings->captive_portal_netmask = $settings['captive_portal_netmask'];
                    }

                    if (isset($settings['captive_portal_gateway'])) {
                        if ($settings['captive_portal_gateway'] !== $locationSettings->captive_portal_gateway) {
                            $increment_version = 1;
                            Log::info('Captive portal gateway updated');
                        }
                        $locationSettings->captive_portal_gateway = $settings['captive_portal_gateway'];
                    }

                    if (isset($settings['captive_auth_method'])) {
                        if ($settings['captive_auth_method'] !== $locationSettings->captive_auth_method) {
                            $increment_version = 1;
                            Log::info('Captive portal auth method updated');
                        }
                        $locationSettings->captive_auth_method = $settings['captive_auth_method'];
                        if ($settings['captive_auth_method'] === 'password') {
                            $locationSettings->captive_portal_password = $settings['captive_portal_password'];
                        }
                    
                        if (isset($settings['captive_social_auth_method'])) {
                            if ($settings['captive_social_auth_method'] !== $locationSettings->captive_social_auth_method) {
                                $increment_version = 1;
                                Log::info('Captive portal social auth method updated');
                            }
                            $locationSettings->captive_social_auth_method = $settings['captive_social_auth_method'];
                        }
                    }
                    if (isset($settings['captive_portal_dns1'])) {
                        if ($settings['captive_portal_dns1'] !== $locationSettings->captive_portal_dns1) {
                            $increment_version = 1;
                            Log::info('Captive portal dns1 updated');
                        }
                        $locationSettings->captive_portal_dns1 = $settings['captive_portal_dns1'];
                    }

                    if (isset($settings['captive_portal_dns2'])) {
                        if ($settings['captive_portal_dns2'] !== $locationSettings->captive_portal_dns2) {
                            $increment_version = 1;
                            Log::info('Captive portal dns2 updated');
                        }
                        $locationSettings->captive_portal_dns2 = $settings['captive_portal_dns2'];
                    }
                
                    // Session settings
                    if (isset($settings['session_timeout'])) {
                        $locationSettings->session_timeout = $settings['session_timeout'];
                    }
                    
                    if (isset($settings['idle_timeout'])) {
                        $locationSettings->idle_timeout = $settings['idle_timeout'];
                    }
                    
                    // Access control
                    if (isset($settings['access_control_enabled'])) {
                        // Convert both values to boolean for proper comparison
                        $newValue = (bool)$settings['access_control_enabled'];
                        $oldValue = (bool)$locationSettings->web_filter_enabled;
                        
                        if ($newValue !== $oldValue) {
                            $increment_version = 1;
                            Log::info('Access control enabled updated');
                            Log::info($newValue);
                            Log::info($oldValue);
                        }
                        $locationSettings->web_filter_enabled = $newValue;
                    }
                    
                    if (isset($settings['allowed_domains'])) {
                        if ($settings['allowed_domains'] !== $locationSettings->web_filter_domains) {
                            $increment_version = 1;
                            Log::info('Allowed domains updated');
                        }
                        $locationSettings->web_filter_domains = $settings['allowed_domains'];
                    }
                    
                    // Bandwidth settings with debugging
                    Log::info('=== BANDWIDTH LIMITS DEBUG ===');
                    Log::info('captive_download_limit in settings: ' . (isset($settings['captive_download_limit']) ? $settings['captive_download_limit'] : 'NOT SET'));
                    Log::info('captive_upload_limit in settings: ' . (isset($settings['captive_upload_limit']) ? $settings['captive_upload_limit'] : 'NOT SET'));
                    Log::info('download_limit in settings: ' . (isset($settings['download_limit']) ? $settings['download_limit'] : 'NOT SET'));
                    Log::info('upload_limit in settings: ' . (isset($settings['upload_limit']) ? $settings['upload_limit'] : 'NOT SET'));
                    Log::info('Current DB download_limit: ' . $locationSettings->download_limit);
                    Log::info('Current DB upload_limit: ' . $locationSettings->upload_limit);
                    
                    if (isset($settings['captive_download_limit'])) {
                        $newValue = $settings['captive_download_limit'];
                        // Convert empty string to null or 0 for proper comparison
                        if ($newValue === '' || $newValue === null) {
                            $newValue = null;
                        } else {
                            $newValue = (int)$newValue;
                        }
                        
                        if ($newValue !== $locationSettings->download_limit) {
                            Log::info('Captive download limit updated from ' . $locationSettings->download_limit . ' to ' . $newValue);
                        }
                        $locationSettings->download_limit = $newValue;
                    }
                    
                    if (isset($settings['captive_upload_limit'])) {
                        $newValue = $settings['captive_upload_limit'];
                        // Convert empty string or "0" to null for proper comparison  
                        if ($newValue === '' || $newValue === null || $newValue === '0') {
                            $newValue = null;
                        } else {
                            $newValue = (int)$newValue;
                        }
                        
                        if ($newValue !== $locationSettings->upload_limit) {
                            Log::info('Captive upload limit updated from ' . $locationSettings->upload_limit . ' to ' . $newValue);
                        }
                        $locationSettings->upload_limit = $newValue;
                    }
                    
                    // Also handle the generic download_limit and upload_limit fields
                    if (isset($settings['download_limit'])) {
                        $newValue = $settings['download_limit'];
                        // Convert empty string to null or 0 for proper comparison
                        if ($newValue === '' || $newValue === null) {
                            $newValue = null;
                        } else {
                            $newValue = (int)$newValue;
                        }
                        
                        if ($newValue !== $locationSettings->download_limit) {
                            Log::info('Download limit updated from ' . $locationSettings->download_limit . ' to ' . $newValue);
                        }
                        $locationSettings->download_limit = $newValue;
                    }
                    
                    if (isset($settings['upload_limit'])) {
                        $newValue = $settings['upload_limit'];
                        // Convert empty string or "0" to null for proper comparison
                        if ($newValue === '' || $newValue === null || $newValue === '0') {
                            $newValue = null;
                        } else {
                            $newValue = (int)$newValue;
                        }
                        
                        if ($newValue !== $locationSettings->upload_limit) {
                            Log::info('Upload limit updated from ' . $locationSettings->upload_limit . ' to ' . $newValue);
                        }
                        $locationSettings->upload_limit = $newValue;
                    }
                    
                    Log::info('Final DB download_limit: ' . $locationSettings->download_limit);
                    Log::info('Final DB upload_limit: ' . $locationSettings->upload_limit);
                    Log::info('=== END BANDWIDTH LIMITS DEBUG ===');
                    
                    // Captive Portal Design ID
                    if (isset($settings['captive_portal_design'])) {
                        if ($settings['captive_portal_design'] !== $locationSettings->captive_portal_design) {
                            Log::info('Captive portal design updated from ' . 
                                $locationSettings->captive_portal_design . ' to ' . 
                                $settings['captive_portal_design']);
                        }
                        $locationSettings->captive_portal_design = $settings['captive_portal_design'];
                    }
                    
                    // Captive Portal VLAN
                    if (isset($settings['captive_portal_vlan'])) {
                        if ($settings['captive_portal_vlan'] !== $locationSettings->captive_portal_vlan) {
                            $increment_version = 1;
                            Log::info('Captive portal VLAN updated');
                        }
                        $locationSettings->captive_portal_vlan = $settings['captive_portal_vlan'];
                    }
                    
                    // Captive Portal Redirect URL
                    if (isset($settings['captive_portal_redirect'])) {
                        if ($settings['captive_portal_redirect'] !== $locationSettings->captive_portal_redirect) {
                            Log::info('Captive portal redirect URL updated');
                        }
                        $locationSettings->captive_portal_redirect = $settings['captive_portal_redirect'];
                    }
                    
                    Log::info('Updating captive portal settings for location: ' . $location->id);
                } 
                elseif ($settingsType === 'wifi') {
                    // Update secured WiFi settings
                    if (isset($settings['wifi_name'])) {
                        if ($settings['wifi_name'] !== $locationSettings->wifi_name) {
                            $increment_version = 1;
                            Log::info('Wifi name updated');
                        }
                        $locationSettings->wifi_name = $settings['wifi_name'];
                    }
                    
                    if (isset($settings['wifi_password'])) {
                        if ($settings['wifi_password'] !== $locationSettings->wifi_password) {
                            $increment_version = 1;
                            Log::info('Wifi password updated');
                        }
                        $locationSettings->wifi_password = $settings['wifi_password'];
                    }
                    
                    if (isset($settings['encryption_type'])) {
                        if ($settings['encryption_type'] !== $locationSettings->wifi_security_type) {
                            $increment_version = 1;
                            Log::info('Wifi security type updated');
                        }
                        $locationSettings->wifi_security_type = $settings['encryption_type'];
                    }
                    
                    // Handle wifi_visible
                    if (isset($settings['wifi_visible'])) {
                        // Convert both values to boolean for proper comparison
                        $newValue = (bool)$settings['wifi_visible'];
                        $oldValue = (bool)$locationSettings->wifi_visible;
                        
                        if ($newValue !== $oldValue) {
                            $increment_version = 1;
                            Log::info('Wifi visible updated');
                            Log::info('New value: ' . ($newValue ? 1 : 0));
                            Log::info('Old value: ' . ($oldValue ? 1 : 0));
                        }
                        $locationSettings->wifi_visible = $newValue;
                        Log::info('Wifi visible updated to: ' . ($newValue ? 1 : 0));
                    }
                    
                    // Access control for secured WiFi
                    if (isset($settings['access_control_enabled'])) {
                        if ($settings['access_control_enabled'] !== $locationSettings->web_filter_enabled) {
                            $increment_version = 1;
                            Log::info('Access control enabled updated');
                        }
                        $locationSettings->web_filter_enabled = $settings['access_control_enabled'];
                    }
                    
                    if (isset($settings['blocked_domains'])) {
                        if ($settings['blocked_domains'] !== $locationSettings->web_filter_domains) {
                            $increment_version = 1;
                            Log::info('Blocked domains updated');
                        }
                        $locationSettings->web_filter_domains = $settings['blocked_domains'];
                    }
                    
                    Log::info('Updating secured WiFi settings for location: ' . $location->id);
                }
                elseif ($settingsType === 'router') {
                    // Update router settings
                    if (isset($settings['wifi_country'])) {
                        if ($settings['wifi_country'] !== $locationSettings->country_code) {
                            $increment_version = 1;
                            Log::info('Wifi country updated');
                        }
                        $locationSettings->country_code = $settings['wifi_country'];
                    }
                    
                    if (isset($settings['power_level_2g'])) {
                        if ($settings['power_level_2g'] !== $locationSettings->transmit_power_2g) {
                            $increment_version = 1;
                            Log::info('Power level 2g updated');
                        }
                        $locationSettings->transmit_power_2g = $settings['power_level_2g'];
                    }
                    
                    if (isset($settings['power_level_5g'])) {
                        if ($settings['power_level_5g'] !== $locationSettings->transmit_power_5g) {
                            $increment_version = 1;
                            Log::info('Power level 5g updated');
                        }
                        $locationSettings->transmit_power_5g = $settings['power_level_5g'];
                    }
                    
                    if (isset($settings['channel_width_2g'])) {
                        if ($settings['channel_width_2g'] !== $locationSettings->channel_width_2g) {
                            $increment_version = 1;
                            Log::info('Channel width 2g updated');
                        }
                        $locationSettings->channel_width_2g = $settings['channel_width_2g'];
                    }
                    
                    if (isset($settings['channel_width_5g'])) {
                        if ($settings['channel_width_5g'] !== $locationSettings->channel_width_5g) {
                            $increment_version = 1;
                            Log::info('Channel width 5g updated');
                        }
                        $locationSettings->channel_width_5g = $settings['channel_width_5g'];
                    }
                    
                    if (isset($settings['channel_2g'])) {
                        if ($settings['channel_2g'] !== $locationSettings->channel_2g) {
                            $increment_version = 1;
                            Log::info('Channel 2g updated');
                        }
                        $locationSettings->channel_2g = $settings['channel_2g'];
                    }
                    
                    if (isset($settings['channel_5g'])) {
                        if ($settings['channel_5g'] !== $locationSettings->channel_5g) {
                            $increment_version = 1;
                            Log::info('Channel 5g updated');
                        }
                        $locationSettings->channel_5g = $settings['channel_5g'];
                    }
                    
                    Log::info('Updating router settings for location: ' . $location->id);
                }
                elseif ($settingsType === 'wan') {
                    Log::info('Updating WAN settings for location: ' . $location->id);
                    Log::info('WAN settings: ');
                    Log::info($settings);
                    // Update WAN settings
                    if (isset($settings['wan_connection_type'])) {
                        Log::info('WAN connection type: ');
                        Log::info($settings['wan_connection_type']);
                        Log::info('Location WAN connection type: ');
                        Log::info($locationSettings->wan_connection_type);
                        if ($settings['wan_connection_type'] !== $locationSettings->wan_connection_type) {
                            $increment_version = 1;
                            Log::info('WAN connection type updated');
                        }
                        $locationSettings->wan_connection_type = $settings['wan_connection_type'];
                    }

                    if (isset($settings['wan_enabled'])) {
                        Log::info('WAN enabled: ');
                        Log::info($settings['wan_enabled']);
                        Log::info('Location WAN enabled: ');
                        Log::info($locationSettings->wan_enabled);
                        if ($settings['wan_enabled'] !== $locationSettings->wan_enabled) {
                            $increment_version = 1;
                            Log::info('WAN enabled status updated');
                        }
                        $locationSettings->wan_enabled = $settings['wan_enabled'];
                    }

                    if (isset($settings['wan_nat_enabled'])) {
                        Log::info('WAN NAT enabled: ');
                        Log::info($settings['wan_nat_enabled']);
                        Log::info('Location WAN NAT enabled: ');
                        Log::info($locationSettings->wan_nat_enabled);
                        if ($settings['wan_nat_enabled'] !== $locationSettings->wan_nat_enabled) {
                            $increment_version = 1;
                            Log::info('WAN NAT status updated');
                        }
                        $locationSettings->wan_nat_enabled = $settings['wan_nat_enabled'];
                    }

                    // Static IP settings
                    if ($settings['wan_connection_type'] === 'static') {
                        if (isset($settings['wan_ip_address'])) {
                            Log::info('WAN IP address: ');
                            Log::info($settings['wan_ip_address']);
                            Log::info('Location WAN IP address: ');
                            Log::info($locationSettings->wan_ip_address);
                            if ($settings['wan_ip_address'] !== $locationSettings->wan_ip_address) {
                                $increment_version = 1;
                                Log::info('WAN IP address updated');
                            }
                            $locationSettings->wan_ip_address = $settings['wan_ip_address'];
                        }

                        if (isset($settings['wan_netmask'])) {
                            Log::info('WAN netmask: ');
                            Log::info($settings['wan_netmask']);
                            Log::info('Location WAN netmask: ');
                            Log::info($locationSettings->wan_netmask);
                            if ($settings['wan_netmask'] !== $locationSettings->wan_netmask) {
                                $increment_version = 1;
                                Log::info('WAN netmask updated');
                            }
                            $locationSettings->wan_netmask = $settings['wan_netmask'];
                        }

                        if (isset($settings['wan_gateway'])) {
                            Log::info('WAN gateway: ');
                            Log::info($settings['wan_gateway']);
                            Log::info('Location WAN gateway: ');
                            Log::info($locationSettings->wan_gateway);
                            if ($settings['wan_gateway'] !== $locationSettings->wan_gateway) {
                                $increment_version = 1;
                                Log::info('WAN gateway updated');
                            }
                            $locationSettings->wan_gateway = $settings['wan_gateway'];
                        }

                        if (isset($settings['wan_primary_dns'])) {
                            Log::info('WAN primary DNS: ');
                            Log::info($settings['wan_primary_dns']);
                            Log::info('Location WAN primary DNS: ');
                            Log::info($locationSettings->wan_primary_dns);
                            if ($settings['wan_primary_dns'] !== $locationSettings->wan_primary_dns) {
                                $increment_version = 1;
                                Log::info('WAN primary DNS updated');
                            }
                            $locationSettings->wan_primary_dns = $settings['wan_primary_dns'];
                        }

                        if (isset($settings['wan_secondary_dns'])) {
                            Log::info('WAN secondary DNS: ');
                            Log::info($settings['wan_secondary_dns']);
                            Log::info('Location WAN secondary DNS: ');
                            Log::info($locationSettings->wan_secondary_dns);
                            if ($settings['wan_secondary_dns'] !== $locationSettings->wan_secondary_dns) {
                                $increment_version = 1;
                                Log::info('WAN secondary DNS updated');
                            }
                            $locationSettings->wan_secondary_dns = $settings['wan_secondary_dns'];
                        }
                    }

                    // PPPoE settings
                    if ($settings['wan_connection_type'] === 'pppoe') {
                        if (isset($settings['wan_pppoe_username'])) {
                            if ($settings['wan_pppoe_username'] !== $locationSettings->wan_pppoe_username) {
                                $increment_version = 1;
                                Log::info('WAN PPPoE username updated');
                            }
                            $locationSettings->wan_pppoe_username = $settings['wan_pppoe_username'];
                        }

                        if (isset($settings['wan_pppoe_password'])) {
                            if ($settings['wan_pppoe_password'] !== $locationSettings->wan_pppoe_password) {
                                $increment_version = 1;
                                Log::info('WAN PPPoE password updated');
                            }
                            $locationSettings->wan_pppoe_password = $settings['wan_pppoe_password'];
                        }

                        if (isset($settings['wan_pppoe_service_name'])) {
                            if ($settings['wan_pppoe_service_name'] !== $locationSettings->wan_pppoe_service_name) {
                                $increment_version = 1;
                                Log::info('WAN PPPoE service name updated');
                            }
                            $locationSettings->wan_pppoe_service_name = $settings['wan_pppoe_service_name'];
                        }
                    }

                    Log::info('Updating WAN settings for location: ' . $location->id);
                } elseif ($settingsType === 'password' || $settingsType === 'password_network') {
                    Log::info('Updating password network settings for location: ' . $location->id);
                    // Update password network settings
                    if (isset($settings['password_wifi_enabled'])) {
                        if ($settings['password_wifi_enabled'] !== $locationSettings->password_wifi_enabled) {
                            $increment_version = 1;
                            Log::info('Password network enabled status updated');
                        }
                        $locationSettings->password_wifi_enabled = $settings['password_wifi_enabled'];
                    }

                    if (isset($settings['password_wifi_ssid'])) {
                        if ($settings['password_wifi_ssid'] !== $locationSettings->password_wifi_ssid) {
                            $increment_version = 1;
                            Log::info('Password network SSID updated');
                        }
                        $locationSettings->password_wifi_ssid = $settings['password_wifi_ssid'];
                    }

                    if (isset($settings['password_wifi_password'])) {
                        if ($settings['password_wifi_password'] !== $locationSettings->password_wifi_password) {
                            $increment_version = 1;
                            Log::info('Password network password updated');
                        }
                        $locationSettings->password_wifi_password = $settings['password_wifi_password'];
                    }

                    if (isset($settings['password_wifi_security'])) {
                        if ($settings['password_wifi_security'] !== $locationSettings->password_wifi_security) {
                            $increment_version = 1;
                            Log::info('Password network security updated');
                        }
                        $locationSettings->password_wifi_security = $settings['password_wifi_security'];
                    }

                    if (isset($settings['password_wifi_cipher_suites'])) {
                        if ($settings['password_wifi_cipher_suites'] !== $locationSettings->password_wifi_cipher_suites) {
                            $increment_version = 1;
                            Log::info('Password network cipher suites updated');
                        }
                        $locationSettings->password_wifi_cipher_suites = $settings['password_wifi_cipher_suites'];
                    }

                    if (isset($settings['password_wifi_ip_mode'])) {
                        if ($settings['password_wifi_ip_mode'] !== $locationSettings->password_wifi_ip_mode) {
                            $increment_version = 1;
                            Log::info('Password network IP mode updated');
                        }
                        $locationSettings->password_wifi_ip_mode = $settings['password_wifi_ip_mode'];
                    }

                    if (isset($settings['password_wifi_ip'])) {
                        if ($settings['password_wifi_ip'] !== $locationSettings->password_wifi_ip) {
                            $increment_version = 1;
                            Log::info('Password network IP updated');
                        }
                        $locationSettings->password_wifi_ip = $settings['password_wifi_ip'];
                    }

                    if (isset($settings['password_wifi_netmask'])) {
                        if ($settings['password_wifi_netmask'] !== $locationSettings->password_wifi_netmask) {
                            $increment_version = 1;
                            Log::info('Password network netmask updated');
                        }
                        $locationSettings->password_wifi_netmask = $settings['password_wifi_netmask'];
                    }

                    if (isset($settings['password_wifi_gateway'])) {
                        if ($settings['password_wifi_gateway'] !== $locationSettings->password_wifi_gateway) {
                            $increment_version = 1;
                            Log::info('Password network gateway updated');
                        }
                        $locationSettings->password_wifi_gateway = $settings['password_wifi_gateway'];
                    }

                    if (isset($settings['password_wifi_dhcp_enabled'])) {
                        if ($settings['password_wifi_dhcp_enabled'] !== $locationSettings->password_wifi_dhcp_enabled) {
                            $increment_version = 1;
                            Log::info('Password network DHCP enabled updated');
                        }
                        $locationSettings->password_wifi_dhcp_enabled = $settings['password_wifi_dhcp_enabled'];
                    }

                    if (isset($settings['password_wifi_dhcp_start'])) {
                        if ($settings['password_wifi_dhcp_start'] !== $locationSettings->password_wifi_dhcp_start) {
                            $increment_version = 1;
                            Log::info('Password network DHCP start updated');
                        }
                        $locationSettings->password_wifi_dhcp_start = $settings['password_wifi_dhcp_start'];
                    }

                    if (isset($settings['password_wifi_dhcp_end'])) {
                        if ($settings['password_wifi_dhcp_end'] !== $locationSettings->password_wifi_dhcp_end) {
                            $increment_version = 1;
                            Log::info('Password network DHCP end updated');
                        }
                        $locationSettings->password_wifi_dhcp_end = $settings['password_wifi_dhcp_end'];
                    }

                    if (isset($settings['password_wifi_vlan'])) {
                        if ($settings['password_wifi_vlan'] !== $locationSettings->password_wifi_vlan) {
                            $increment_version = 1;
                            Log::info('Password network VLAN updated');
                        }
                        $locationSettings->password_wifi_vlan = $settings['password_wifi_vlan'];
                    }

                    // handke password_wifi_visible 
                    if (isset($settings['password_wifi_visible'])) {
                        // Convert both values to boolean for proper comparison
                        $newValue = (bool)$settings['password_wifi_visible'];
                        $oldValue = (bool)$locationSettings->wifi_visible;
                        
                        if ($newValue !== $oldValue) {
                            $increment_version = 1;
                            Log::info('Wifi visible updated');
                            Log::info('New value: ' . ($newValue ? 1 : 0));
                            Log::info('Old value: ' . ($oldValue ? 1 : 0));
                        }
                        $locationSettings->wifi_visible = $newValue;
                        Log::info('Wifi visible updated to: ' . ($newValue ? 1 : 0));
                    }

                    Log::info('Updating password network settings for location: ' . $location->id);
                } elseif ($settingsType === 'location_info') {
                    Log::info('Updating location info settings for location: ' . $location->id);
                    
                    // Check for address changes BEFORE updating the location fields
                    $addressFields = ['address', 'city', 'state', 'country', 'postal_code'];
                    $hasAddressChange = false;
                    foreach ($addressFields as $field) {
                        if (isset($settings[$field]) && $settings[$field] !== $location->$field) {
                            $hasAddressChange = true;
                            Log::info("Address field '{$field}' changed from '{$location->$field}' to '{$settings[$field]}'");
                            break;
                        }
                    }
                    
                    if (isset($settings['name'])) {
                        if ($settings['name'] !== $location->name) {
                            // $increment_version = 1;
                            Log::info('Location name updated');
                        }
                        $location->name = $settings['name'];
                    }

                    if (isset($settings['address'])) {
                        if ($settings['address'] !== $location->address) {
                            // $increment_version = 1;
                            Log::info('Location address updated');
                        }
                        $location->address = $settings['address'];
                    }

                    if (isset($settings['city'])) {
                        if ($settings['city'] !== $location->city) {
                            // $increment_version = 1;
                            Log::info('Location city updated');
                        }
                        $location->city = $settings['city'];
                    }

                    if (isset($settings['state'])) {
                        if ($settings['state'] !== $location->state) {
                            // $increment_version = 1;
                            Log::info('Location state updated');
                        }
                        $location->state = $settings['state'];
                    }

                    if (isset($settings['country'])) {
                        if ($settings['country'] !== $location->country) {
                            // $increment_version = 1;
                            Log::info('Location country updated');
                        }
                        $location->country = $settings['country'];
                    }

                    if (isset($settings['manager_name'])) {
                        if ($settings['manager_name'] !== $location->manager_name) {
                            // $increment_version = 1;
                            Log::info('Location manager name updated');
                        }
                        $location->manager_name = $settings['manager_name'];
                    }

                    if (isset($settings['contact_email'])) {
                        if ($settings['contact_email'] !== $location->contact_email) {
                            // $increment_version = 1;
                            Log::info('Location contact email updated');
                        }
                        $location->contact_email = $settings['contact_email'];
                    }

                    if (isset($settings['contact_phone'])) {
                        if ($settings['contact_phone'] !== $location->contact_phone) {
                            // $increment_version = 1;
                            Log::info('Location contact phone updated');
                        }
                        $location->contact_phone = $settings['contact_phone'];
                    }

                    if (isset($settings['status'])) {
                        if ($settings['status'] !== $location->status) {
                            // $increment_version = 1;
                            Log::info('Location status updated');
                        }
                        $location->status = $settings['status'];
                    }

                    if (isset($settings['description'])) {
                        if ($settings['description'] !== $location->description) {
                            // $increment_version = 1;
                            Log::info('Location description updated');
                        }
                        $location->description = $settings['description'];
                    }

                    if (isset($settings['owner_id'])) {
                        // Check if user has admin role before allowing owner_id changes
                        $currentUser = Auth::guard('api')->user();
                        if (!$currentUser || $currentUser->role !== 'admin') {
                            Log::warning('Non-admin user attempted to change location owner', [
                                'user_id' => $currentUser ? $currentUser->id : null,
                                'user_role' => $currentUser ? $currentUser->role : null,
                                'location_id' => $location->id
                            ]);
                            return response()->json([
                                'success' => false,
                                'message' => 'Only administrators can change location ownership'
                            ], 403);
                        }
                        
                        if ($settings['owner_id'] !== $location->owner_id) {
                            Log::info('Location owner updated by admin', [
                                'admin_id' => $currentUser->id,
                                'location_id' => $location->id,
                                'old_owner_id' => $location->owner_id,
                                'new_owner_id' => $settings['owner_id']
                            ]);
                        }
                        $location->owner_id = $settings['owner_id'];
                    }

                    if (isset($settings['latitude'])) {
                        if ($settings['latitude'] !== $location->latitude) {
                            // $increment_version = 1;
                            Log::info('Location latitude updated');
                        }
                        $location->latitude = $settings['latitude'];
                    }

                    if (isset($settings['longitude'])) {
                        if ($settings['longitude'] !== $location->longitude) {
                            // $increment_version = 1;
                            Log::info('Location longitude updated');
                        }
                        $location->longitude = $settings['longitude'];
                    }

                    // Geocode address if address fields have actually changed and lat/lng not explicitly provided
                    Log::info('Geocoding evaluation: hasAddressChange=' . ($hasAddressChange ? 'true' : 'false') . ', hasLatitude=' . (isset($settings['latitude']) ? 'true' : 'false') . ', hasLongitude=' . (isset($settings['longitude']) ? 'true' : 'false'));
                    
                    if ($hasAddressChange && !isset($settings['latitude']) && !isset($settings['longitude'])) {
                        Log::info('Address fields updated in location_info, attempting to geocode');
                        $geocodingService = new GeocodingService();
                        $geocodeResult = $geocodingService->geocodeAddress(
                            $settings['address'] ?? $location->address,
                            $settings['city'] ?? $location->city,
                            $settings['state'] ?? $location->state,
                            $settings['country'] ?? $location->country,
                            $settings['postal_code'] ?? $location->postal_code
                        );
                        
                        if ($geocodeResult) {
                            $location->latitude = $geocodeResult['lat'];
                            $location->longitude = $geocodeResult['lng'];
                            Log::info('Successfully geocoded updated address in location_info', [
                                'latitude' => $geocodeResult['lat'],
                                'longitude' => $geocodeResult['lng'],
                                'formatted_address' => $geocodeResult['formatted_address']
                            ]);
                        } else {
                            Log::warning('Failed to geocode updated address in location_info');
                        }
                    }

                    // Save the location with updated info
                    $location->save();
                }
                if ($increment_version == 1) {
                    // $locationSettings->configuration_version = $locationSettings->configuration_version + 1;
                    $device->configuration_version = $device->configuration_version + 1;
                    $device->save();
                    Log::info('Device configuration version incremented to: ' . $device->configuration_version);
                }
                
                // Save the settings
                // Log::info('=== SAVING LOCATION SETTINGS ===');
                // Log::info('Location settings before save: ' . json_encode($locationSettings->toArray()));
                $locationSettings->save();
                // Log::info('Location settings saved successfully');
                // Log::info('Location settings after save: ' . json_encode($locationSettings->fresh()->toArray()));
                
                return response()->json([
                    'success' => true,
                    'message' => 'Settings updated successfully',
                    'location' => $location,
                    'settings' => $locationSettings

                ]);
            } catch (\Exception $e) {
                Log::error('Error updating location settings: ' . $e->getMessage());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating settings: ' . $e->getMessage()
                ], 500);
            }
        }
        
        // Check if it's a device update
        if ($request->has('device')) {
            $deviceData = $request->input('device');
            $location = Location::find($location_id);
            
            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found'
                ], 404);
            }
            
            $device = Device::find($location->device_id);
            
            if (!$device) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device not found for this location'
                ], 404);
            }
            
            try {
                // Handle device model update
                if (isset($deviceData['product_model_id'])) {
                    $newProductModelId = $deviceData['product_model_id'] ?: null;
                    $oldProductModelId = $device->product_model_id;

                    // Validate product_model_id exists
                    if ($newProductModelId && !\App\Models\ProductModel::find($newProductModelId)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid device model selected.'
                        ], 400);
                    }

                    Log::info("Updating device product_model_id from '{$oldProductModelId}' to '{$newProductModelId}' for device: " . $device->id);

                    $device->product_model_id = $newProductModelId;

                    // If model changed, increment configuration version
                    if ($oldProductModelId !== $newProductModelId) {
                        $device->configuration_version = $device->configuration_version + 1;
                        Log::info('Device configuration version incremented to: ' . $device->configuration_version);
                    }

                    $device->save();
                    $device->load('productModel');

                    Log::info('Device model updated successfully');

                    return response()->json([
                        'success' => true,
                        'message' => 'Device model updated successfully',
                        'data' => [
                            'device' => $device,
                            'old_model' => $oldProductModelId,
                            'new_model' => $newProductModelId
                        ]
                    ]);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'No device updates specified'
                ], 400);
                
            } catch (\Exception $e) {
                Log::error('Error updating device: ' . $e->getMessage());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating device: ' . $e->getMessage()
                ], 500);
            }
        }
        
        // If it's not a settings update or device update, handle regular location update
        // (This part could be implemented later for updating location details)
        return response()->json([
            'success' => false,
            'message' => 'No valid update data detected. Expected settings, device, or location data.'
        ]);
    }

    /**
     * Remove the specified location from storage.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function destroy(Location $location)
    {
        $location->delete();

        return response()->json([
            'success' => true,
            'message' => 'Location deleted successfully'
        ]);
    }

    public function updateGeneral(Request $request, $location_id)
    {
        // Log::info('Update general location information received');
        // Log::info($request->all());
        
        try {
            $location = Location::find($location_id);
            
            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found'
                ], 404);
            }
            
            // Check if owner_id or shared_users are being updated — admin/superadmin only
            if ($request->hasAny(['owner_id', 'shared_users'])) {
                $currentUser = Auth::guard('api')->user();
                if (!$currentUser || !in_array($currentUser->role, ['admin', 'superadmin'])) {
                    // Log::warning('Non-admin user attempted to change location owner via updateGeneral', [
                    //     'user_id' => $currentUser ? $currentUser->id : null,
                    //     'user_role' => $currentUser ? $currentUser->role : null,
                    //     'location_id' => $location_id
                    // ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Only administrators can change location ownership or shared access'
                    ], 403);
                }
            }
            
            // Validate the request data
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'address' => 'sometimes|nullable|string',
                'city' => 'sometimes|nullable|string|max:255',
                'state' => 'sometimes|nullable|string|max:255',
                'country' => 'sometimes|nullable|string|max:255',
                'postal_code' => 'sometimes|nullable|string|max:255',
                'latitude' => 'sometimes|nullable|numeric',
                'longitude' => 'sometimes|nullable|numeric',
                'description' => 'sometimes|nullable|string',
                'manager_name' => 'sometimes|nullable|string|max:255',
                'contact_email' => 'sometimes|nullable|email|max:255',
                'contact_phone' => 'sometimes|nullable|string|max:255',
                'status' => 'sometimes|nullable|string|in:active,inactive,maintenance',
                'owner_id' => 'sometimes|nullable|exists:users,id',
                'shared_users' => 'sometimes|nullable|array',
                'shared_users.*.user_id' => 'required_with:shared_users|integer|exists:users,id',
                'shared_users.*.access_level' => 'required_with:shared_users|string|in:full,partial,read_only',
            ]);
            // Log::info('Validated data: ');
            // Log::info($validated);
            
            // Geocode address if address fields have actually changed and lat/lng not provided
            $addressFields = ['address', 'city', 'state', 'country', 'postal_code'];
            $hasAddressChange = false;
            foreach ($addressFields as $field) {
                if (array_key_exists($field, $validated) && $validated[$field] !== $location->$field) {
                    $hasAddressChange = true;
                    Log::info("Address field '{$field}' changed from '{$location->$field}' to '{$validated[$field]}'");
                    break;
                }
            }
            
            if ($hasAddressChange && !isset($validated['latitude']) && !isset($validated['longitude'])) {
                Log::info('Address fields updated, attempting to geocode');
                $geocodingService = new GeocodingService();
                $geocodeResult = $geocodingService->geocodeAddress(
                    $validated['address'] ?? $location->address,
                    $validated['city'] ?? $location->city,
                    $validated['state'] ?? $location->state,
                    $validated['country'] ?? $location->country,
                    $validated['postal_code'] ?? $location->postal_code
                );
                
                if ($geocodeResult) {
                    $validated['latitude'] = $geocodeResult['lat'];
                    $validated['longitude'] = $geocodeResult['lng'];
                    Log::info('Successfully geocoded updated address', [
                        'latitude' => $geocodeResult['lat'],
                        'longitude' => $geocodeResult['lng'],
                        'formatted_address' => $geocodeResult['formatted_address']
                    ]);
                } else {
                    Log::warning('Failed to geocode updated address');
                }
            }
            
            // Log owner_id changes if any
            if (isset($validated['owner_id']) && $validated['owner_id'] !== $location->owner_id) {
                $currentUser = Auth::guard('api')->user();
                Log::info('Location owner updated via updateGeneral', [
                    'admin_id' => $currentUser->id,
                    'location_id' => $location_id,
                    'old_owner_id' => $location->owner_id,
                    'new_owner_id' => $validated['owner_id']
                ]);
            }
            
            // Persist shared_users separately (JSON cast handles encoding)
            if (array_key_exists('shared_users', $validated)) {
                $location->shared_users = $validated['shared_users'] ?? [];
                unset($validated['shared_users']);
            }

            // Update the location with validated data
            $location->update($validated);
            
            // Get associated data
            $device = Device::find($location->device_id);
            $locationSettings = LocationSettingsV2::where('location_id', $location_id)->first();
            
            // Prepare response data
            $locationData = $location->toArray();
            $locationData['settings'] = $locationSettings;
            $locationData['device'] = $device;
            
            return response()->json([
                'success' => true,
                'message' => 'Location information updated successfully',
                'location' => $locationData
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating general location information: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating location information: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update device firmware for a location
     */
    public function updateFirmware(Request $request, $id)
    {
        // Log::info('Update firmware request received for location: ' . $id);
        // Log::info($request->all());
        
        try {
            $request->validate([
                'firmware_id' => 'required|exists:firmware,id',
                'firmware_version' => 'nullable|string'
            ]);
            
            $location = Location::find($id);
            
            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found'
                ], 404);
            }
            
            $device = Device::find($location->device_id);
            
            if (!$device) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device not found for this location'
                ], 404);
            }
            
            // Get the firmware information
            $firmware = \App\Models\Firmware::find($request->firmware_id);
            
            if (!$firmware) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firmware not found'
                ], 404);
            }
            
            if (!$firmware->is_enabled) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected firmware is not enabled'
                ], 400);
            }
            
            // Check if firmware is compatible with device model
            $deviceType = $device->productModel?->device_type;
            if ($deviceType && $firmware->model && $firmware->model !== $deviceType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firmware is not compatible with device model'
                ], 400);
            }
            
            // Update device firmware version
            $device->firmware_version = $request->firmware_version ?: $firmware->name;
            $device->firmware_id = $firmware->id;
            
            // Increment configuration version to trigger device update
            // $device->configuration_version = $device->configuration_version + 1;
            
            $device->save();
            
            Log::info('Device firmware updated successfully for device: ' . $device->id);
            
            return response()->json([
                'success' => true,
                'message' => 'Firmware update initiated successfully',
                'data' => [
                    'device' => $device,
                    'firmware' => $firmware
                ]
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating device firmware: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating device firmware: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get channel scan data for a location's device
     */

    /**
     * Get location settings
     */
    /**
     * Enable or disable QoS for a location.
     * PUT /api/v1/locations/{id}/settings/qos
     * Body: { "enabled": true|false }
     */
    public function updateQosSettings(Request $request, $id)
    {
        try {
            $location = Location::find($id);
            if (!$location) {
                return response()->json(['success' => false, 'message' => 'Location not found'], 404);
            }

            $validated = $request->validate(['enabled' => 'required|boolean']);

            $settings = LocationSettingsV2::firstOrCreate(
                ['location_id' => $id],
                ['web_filter_enabled' => false, 'web_filter_categories' => [], 'qos_enabled' => false]
            );

            $previousQos = (bool) $settings->qos_enabled;
            $settings->qos_enabled = $validated['enabled'];
            $settings->save();

            // Increment device config version whenever QoS is toggled
            $configVersionIncremented = false;
            if ((bool)$validated['enabled'] !== $previousQos) {
                $device = \App\Models\Device::where('id', $location->device_id)->first();
                if ($device) {
                    $device->configuration_version = $device->configuration_version + 1;
                    $device->save();
                    $configVersionIncremented = true;
                    Log::info('Device config version incremented to ' . $device->configuration_version . ' after QoS toggle for location: ' . $id);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'QoS ' . ($validated['enabled'] ? 'enabled' : 'disabled') . '.',
                'data'    => [
                    'qos_enabled'               => $settings->qos_enabled,
                    'config_version_incremented' => $configVersionIncremented,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating QoS settings: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error updating QoS settings: ' . $e->getMessage()], 500);
        }
    }

    public function getSettings($id)
    {
        try {
            $location = Location::find($id);
            
            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found'
                ], 404);
            }
            
            $settings = LocationSettingsV2::where('location_id', $id)->first();
            
            if (!$settings) {
                // Create default settings if none exist
                $settings = new LocationSettingsV2([
                    'location_id' => $id,
                    'web_filter_enabled' => false,
                    'web_filter_categories' => [],
                ]);
                $settings->save();
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'settings' => $settings
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting location settings: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error getting location settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update location settings
     */
    public function updateSettings(Request $request, $id)
    {
        // Log::info('Update location settings request received for location: ' . $id);
        // Log::info($request->all());
        
        try {
            $location = Location::find($id);
            
            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found'
                ], 404);
            }
            
            $settings = LocationSettingsV2::where('location_id', $id)->first();
            
            if (!$settings) {
                // Create new settings if none exist
                $settings = new LocationSettingsV2(['location_id' => $id]);
            }
            
            // Get the device for config version increment
            $device = Device::find($location->device_id);
            $increment_version = 0;
            
            // Store original values for comparison
            $originalSettings = [
                'country_code'       => $settings->country_code,
                'transmit_power_2g'  => $settings->transmit_power_2g,
                'transmit_power_5g'  => $settings->transmit_power_5g,
                'channel_2g'         => $settings->channel_2g,
                'channel_5g'         => $settings->channel_5g,
                'channel_width_2g'   => $settings->channel_width_2g,
                'channel_width_5g'   => $settings->channel_width_5g,
                'vlan_enabled'            => $settings->vlan_enabled,
                'wan_connection_type'     => $settings->wan_connection_type,
                'wan_ip_address'          => $settings->wan_ip_address,
                'wan_netmask'             => $settings->wan_netmask,
                'wan_gateway'             => $settings->wan_gateway,
                'wan_primary_dns'         => $settings->wan_primary_dns,
                'wan_secondary_dns'       => $settings->wan_secondary_dns,
                'wan_pppoe_username'      => $settings->wan_pppoe_username,
                'wan_pppoe_password'      => $settings->wan_pppoe_password,
                'wan_pppoe_service_name'  => $settings->wan_pppoe_service_name,
                'wan_enabled'             => $settings->wan_enabled,
                'wan_mac_address'         => $settings->wan_mac_address,
                'wan_mtu'                 => $settings->wan_mtu,
                'wan_nat_enabled'         => $settings->wan_nat_enabled,
                'web_filter_categories'   => $settings->web_filter_categories,
                'web_filter_enabled'      => $settings->web_filter_enabled,
            ];
            
            // Only accept fields that exist in location_settings_v2
            $settingsData = $request->only([
                'country_code',
                'transmit_power_2g',
                'transmit_power_5g',
                'channel_2g',
                'channel_5g',
                'channel_width_2g',
                'channel_width_5g',
                'vlan_enabled',
                'wan_enabled',
                'wan_connection_type',
                'wan_ip_address',
                'wan_netmask',
                'wan_gateway',
                'wan_primary_dns',
                'wan_secondary_dns',
                'wan_pppoe_username',
                'wan_pppoe_password',
                'wan_pppoe_service_name',
                'wan_mac_address',
                'wan_mtu',
                'wan_nat_enabled',
                'web_filter_enabled',
                'web_filter_domains',
                'web_filter_categories',
                'qos_enabled',
            ]);
            
            // Check for router setting changes that require config version increment
            $routerSettingsChanged = false;
            
            // Country/Region changes
            if (isset($settingsData['country_code']) && $settingsData['country_code'] !== $originalSettings['country_code']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('Country code updated from "' . $originalSettings['country_code'] . '" to "' . $settingsData['country_code'] . '"');
            }
            
            // Transmit Power changes
            if (isset($settingsData['transmit_power_2g']) && $settingsData['transmit_power_2g'] !== $originalSettings['transmit_power_2g']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('Transmit power 2G updated from "' . $originalSettings['transmit_power_2g'] . '" to "' . $settingsData['transmit_power_2g'] . '"');
            }
            
            if (isset($settingsData['transmit_power_5g']) && $settingsData['transmit_power_5g'] !== $originalSettings['transmit_power_5g']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('Transmit power 5G updated from "' . $originalSettings['transmit_power_5g'] . '" to "' . $settingsData['transmit_power_5g'] . '"');
            }
            
            // Channel changes
            if (isset($settingsData['channel_2g']) && $settingsData['channel_2g'] !== $originalSettings['channel_2g']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('Channel 2G updated from "' . $originalSettings['channel_2g'] . '" to "' . $settingsData['channel_2g'] . '"');
            }
            
            if (isset($settingsData['channel_5g']) && $settingsData['channel_5g'] !== $originalSettings['channel_5g']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('Channel 5G updated from "' . $originalSettings['channel_5g'] . '" to "' . $settingsData['channel_5g'] . '"');
            }
            
            // Channel Width changes
            if (isset($settingsData['channel_width_2g']) && $settingsData['channel_width_2g'] !== $originalSettings['channel_width_2g']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('Channel width 2G updated from "' . $originalSettings['channel_width_2g'] . '" to "' . $settingsData['channel_width_2g'] . '"');
            }
            
            if (isset($settingsData['channel_width_5g']) && $settingsData['channel_width_5g'] !== $originalSettings['channel_width_5g']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('Channel width 5G updated from "' . $originalSettings['channel_width_5g'] . '" to "' . $settingsData['channel_width_5g'] . '"');
            }
            
            // Global VLAN enable/disable changes
            if (isset($settingsData['vlan_enabled']) && $settingsData['vlan_enabled'] !== $originalSettings['vlan_enabled']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('VLAN enabled updated from "' . ($originalSettings['vlan_enabled'] ? 'true' : 'false') . '" to "' . ($settingsData['vlan_enabled'] ? 'true' : 'false') . '"');
            }

            // Web filter enable/disable always bumps config version
            if (isset($settingsData['web_filter_enabled'])
                && (bool)$settingsData['web_filter_enabled'] !== (bool)$originalSettings['web_filter_enabled']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('Web filter enabled updated from "' . ($originalSettings['web_filter_enabled'] ? 'true' : 'false') . '" to "' . ($settingsData['web_filter_enabled'] ? 'true' : 'false') . '"');
            }

            // Handle web_filter_categories comparison
            if (isset($settingsData['web_filter_categories'])) {
                // Ensure web_filter_categories is properly handled as JSON
                if (is_string($settingsData['web_filter_categories'])) {
                    $settingsData['web_filter_categories'] = json_decode($settingsData['web_filter_categories'], true) ?: [];
                } elseif (!is_array($settingsData['web_filter_categories'])) {
                    $settingsData['web_filter_categories'] = [];
                }

                // Compare web_filter_categories with proper ordering
                $newCategories = $this->sortCategoriesForComparison($settingsData['web_filter_categories']);
                $oldCategories = $this->sortCategoriesForComparison($originalSettings['web_filter_categories'] ?: []);

                // Resolve whether web filtering will be active after this save
                $filteringEnabled = isset($settingsData['web_filter_enabled'])
                    ? (bool)$settingsData['web_filter_enabled']
                    : (bool)$originalSettings['web_filter_enabled'];

                if (json_encode($newCategories) !== json_encode($oldCategories) && $filteringEnabled) {
                    $increment_version = 1;
                    $routerSettingsChanged = true;
                    Log::info('Web filter categories updated', [
                        'old_categories' => $oldCategories,
                        'new_categories' => $newCategories
                    ]);
                }
            }

            // WAN settings changes
            if (isset($settingsData['wan_connection_type']) && $settingsData['wan_connection_type'] !== $originalSettings['wan_connection_type']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('WAN connection type updated from "' . $originalSettings['wan_connection_type'] . '" to "' . $settingsData['wan_connection_type'] . '"');
            }
            
            if (isset($settingsData['wan_enabled']) && $settingsData['wan_enabled'] !== $originalSettings['wan_enabled']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('WAN enabled updated from "' . ($originalSettings['wan_enabled'] ? 'true' : 'false') . '" to "' . ($settingsData['wan_enabled'] ? 'true' : 'false') . '"');
            }
            
            if (isset($settingsData['wan_nat_enabled']) && $settingsData['wan_nat_enabled'] !== $originalSettings['wan_nat_enabled']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('WAN NAT enabled updated from "' . ($originalSettings['wan_nat_enabled'] ? 'true' : 'false') . '" to "' . ($settingsData['wan_nat_enabled'] ? 'true' : 'false') . '"');
            }

            if (isset($settingsData['wan_ip_address']) && $settingsData['wan_ip_address'] !== $originalSettings['wan_ip_address']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('WAN IP address updated from "' . $originalSettings['wan_ip_address'] . '" to "' . $settingsData['wan_ip_address'] . '"');
            }

            if (isset($settingsData['wan_netmask']) && $settingsData['wan_netmask'] !== $originalSettings['wan_netmask']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('WAN netmask updated from "' . $originalSettings['wan_netmask'] . '" to "' . $settingsData['wan_netmask'] . '"');
            }

            if (isset($settingsData['wan_gateway']) && $settingsData['wan_gateway'] !== $originalSettings['wan_gateway']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('WAN gateway updated from "' . $originalSettings['wan_gateway'] . '" to "' . $settingsData['wan_gateway'] . '"');
            }

            if (isset($settingsData['wan_primary_dns']) && $settingsData['wan_primary_dns'] !== $originalSettings['wan_primary_dns']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('WAN primary DNS updated from "' . $originalSettings['wan_primary_dns'] . '" to "' . $settingsData['wan_primary_dns'] . '"');
            }

            if (isset($settingsData['wan_secondary_dns']) && $settingsData['wan_secondary_dns'] !== $originalSettings['wan_secondary_dns']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('WAN secondary DNS updated from "' . $originalSettings['wan_secondary_dns'] . '" to "' . $settingsData['wan_secondary_dns'] . '"');
            }

            if (isset($settingsData['wan_pppoe_username']) && $settingsData['wan_pppoe_username'] !== $originalSettings['wan_pppoe_username']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('WAN PPPoE username updated from "' . $originalSettings['wan_pppoe_username'] . '" to "' . $settingsData['wan_pppoe_username'] . '"');
            }

            if (isset($settingsData['wan_pppoe_password']) && $settingsData['wan_pppoe_password'] !== $originalSettings['wan_pppoe_password']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('WAN PPPoE password updated from "' . $originalSettings['wan_pppoe_password'] . '" to "' . $settingsData['wan_pppoe_password'] . '"');
            }

            if (isset($settingsData['wan_pppoe_service_name']) && $settingsData['wan_pppoe_service_name'] !== $originalSettings['wan_pppoe_service_name']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('WAN PPPoE service name updated from "' . $originalSettings['wan_pppoe_service_name'] . '" to "' . $settingsData['wan_pppoe_service_name'] . '"');
            }

            if (isset($settingsData['wan_mac_address']) && $settingsData['wan_mac_address'] !== $originalSettings['wan_mac_address']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('WAN MAC address updated from "' . $originalSettings['wan_mac_address'] . '" to "' . $settingsData['wan_mac_address'] . '"');
            }

            if (isset($settingsData['wan_mtu']) && $settingsData['wan_mtu'] !== $originalSettings['wan_mtu']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('WAN MTU updated from "' . $originalSettings['wan_mtu'] . '" to "' . $settingsData['wan_mtu'] . '"');
            }
            
            // Handle Captive Portal MAC filter list (adds to radcheck, no config version increment)
            // Handle Captive Portal MAC filter list (adds to radcheck, no config version increment)
            if (isset($settingsData['captive_mac_filter_list'])) {
                $normalizedMacList = $this->normalizeMacFilterList($settingsData['captive_mac_filter_list']);
                
                // Check if MAC filter list changed
                $currentMacList = $settings->captive_mac_filter_list ?: [];
                if (json_encode($normalizedMacList) !== json_encode($currentMacList)) {
                    $increment_version = 1;
                    $routerSettingsChanged = true;
                    // Log::info('Captive portal MAC filter list updated (no config version increment)', [
                    //     'old_list' => $currentMacList,
                    //     'new_list' => $normalizedMacList,
                    //     'scope_info' => 'Each MAC address includes scope: block_24, block_5, or all'
                    // ]);
                    
                    // Update the captive portal MAC filter list first
                    // This includes mac, type, and scope fields for each entry
                    $settings->captive_mac_filter_list = $normalizedMacList;
                    
                    // Handle radcheck records for captive portal MAC address filtering
                    // Note: MAC filter scope is now stored per LocationNetwork, not in radcheck table
                    // $this->updateRadcheckForMacFiltering($settings, $currentMacList, $normalizedMacList);
                }
                
                unset($settingsData['captive_mac_filter_list']);
            }
            
            // Handle Secured WiFi MAC filter list (no radcheck, increments config version)
            if (isset($settingsData['secured_mac_filter_list'])) {
                $normalizedMacList = $this->normalizeMacFilterList($settingsData['secured_mac_filter_list']);
                
                // Check if MAC filter list changed
                $currentMacList = $settings->secured_mac_filter_list ?: [];
                if (json_encode($normalizedMacList) !== json_encode($currentMacList)) {
                    $increment_version = 1;
                    // Log::info('Secured WiFi MAC filter list updated (config version increment)', [
                    //     'old_list' => $currentMacList,
                    //     'new_list' => $normalizedMacList,
                    //     'scope_info' => 'Each MAC address includes scope: block_24, block_5, or all'
                    // ]);
                    
                    // Update the secured WiFi MAC filter list (no radcheck records for secured WiFi)
                    // This includes mac, type, and scope fields for each entry
                    $settings->secured_mac_filter_list = $normalizedMacList;
                }
                
                unset($settingsData['secured_mac_filter_list']);
            }
            
            // Handle legacy mac_filter_list field for backward compatibility
            // Default to captive portal behavior (radcheck, no config version increment)
            if (isset($settingsData['mac_filter_list'])) {
                $normalizedMacList = $this->normalizeMacFilterList($settingsData['mac_filter_list']);
                
                // Check if MAC filter list changed
                $currentMacList = $settings->mac_filter_list ?: [];
                if (json_encode($normalizedMacList) !== json_encode($currentMacList)) {
                    // Log::info('Legacy MAC filter list updated (treated as captive portal)', [
                    //     'old_list' => $currentMacList,
                    //     'new_list' => $normalizedMacList
                    // ]);
                    
                    // Update the MAC filter list and handle radcheck records.
                    // Use the first captive portal network for this location as the network scope.
                    $settings->mac_filter_list = $normalizedMacList;
                    $captiveNet = \App\Models\LocationNetwork::where('location_id', $settings->location_id)
                        ->where('type', 'captive_portal')
                        ->first();
                    if ($captiveNet) {
                        $this->updateRadcheckForMacFiltering($captiveNet->id, $currentMacList, $normalizedMacList);
                    }
                }
                
                unset($settingsData['mac_filter_list']);
            }
            
            // Apply the settings changes
            $settings->fill($settingsData);
            $settings->save();
            
            // Increment device configuration version if router settings changed
            if ($increment_version == 1 && $device) {
                $oldVersion = $device->configuration_version;
                $device->configuration_version = $device->configuration_version + 1;
                $device->save();
                Log::info('Device configuration version incremented from ' . $oldVersion . ' to ' . $device->configuration_version . ' for location: ' . $id);
            }
            
            Log::info('Location settings updated successfully for location: ' . $id);
            
            // Prepare response data
            $responseData = [
                'settings' => $settings
            ];
            
            // Add config version information to response if it was incremented
            if ($increment_version == 1 && $device) {
                $responseData['config_version_incremented'] = true;
                $responseData['new_config_version'] = $device->configuration_version;
                $responseData['previous_config_version'] = $device->configuration_version - 1;
            } else {
                $responseData['config_version_incremented'] = false;
                $responseData['current_config_version'] = $device ? $device->configuration_version : null;
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Location settings updated successfully',
                'data' => $responseData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating location settings: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating location settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test geocoding functionality
     */
    public function testGeocode(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'country' => 'nullable|string',
            'postal_code' => 'nullable|string',
        ]);

        $geocodingService = new GeocodingService();
        $result = $geocodingService->geocodeAddress(
            $request->address,
            $request->city,
            $request->state,
            $request->country,
            $request->postal_code
        );

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Geocoding successful',
                'data' => $result
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Geocoding failed'
            ], 400);
        }
    }

    /**
     * Update MAC address for the device associated with this location
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateMacAddress(Request $request, $id)
    {
        try {
            Log::info('Updating device assignment / MAC address for location: ' . $id);

            $request->validate([
                'mac_address' => 'nullable|string|regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/',
                'device_id'   => 'nullable|integer|exists:devices,id',
            ]);

            $location = Location::find($id);
            if (!$location) {
                return response()->json(['success' => false, 'message' => 'Location not found'], 404);
            }

            // If a device_id is supplied, reassign the location to that device first
            if ($request->filled('device_id')) {
                $newDevice = Device::findOrFail($request->device_id);

                // Detach the new device from any other location it may currently be on
                $previousLocation = Location::where('device_id', $newDevice->id)
                    ->where('id', '!=', $location->id)
                    ->first();
                if ($previousLocation) {
                    $previousLocation->device_id = null;
                    $previousLocation->save();
                }

                // Detach whatever device was previously on this location
                $location->device_id = $newDevice->id;
                $location->save();

                $device = $newDevice;
            } else {
                $device = $location->device;
                if (!$device) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No device associated with this location. Pass device_id to assign one.',
                    ], 404);
                }
            }

            // If a mac_address was also provided, update it on the device
            $oldMacAddress = $device->mac_address;
            if ($request->filled('mac_address')) {
                $newMacAddress = str_replace(':', '-', $request->mac_address);

                $conflict = Device::where('mac_address', $newMacAddress)
                    ->where('id', '!=', $device->id)
                    ->first();
                if ($conflict) {
                    return response()->json([
                        'success' => false,
                        'message' => 'MAC address is already in use by another device',
                    ], 409);
                }

                $device->mac_address = $newMacAddress;
                $device->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Device assigned successfully',
                'data' => [
                    'device'          => $device,
                    'old_mac_address' => $oldMacAddress,
                    'new_mac_address' => $device->mac_address,
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating device/MAC for location: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update radcheck records for MAC address filtering
     *
     * @param \App\Models\LocationSettingsV2 $settings
     * @param array $oldMacList
     * @param array $newMacList
     * @return void
     */
    /**
     * Sync radcheck MAC filter records for a specific network.
     *
     * @param int   $networkId   The LocationNetwork id to scope radcheck records
     * @param array $oldMacList
     * @param array $newMacList
     */
    private function updateRadcheckForMacFiltering(int $networkId, array $oldMacList, array $newMacList): void
    {
        $toMap = function (array $list): array {
            $map = [];
            foreach ($list as $item) {
                if (is_string($item)) {
                    $map[$item] = ['type' => 'blacklist', 'scope' => 'all'];
                } elseif (is_array($item) && isset($item['mac'], $item['type'])) {
                    $map[$item['mac']] = ['type' => $item['type'], 'scope' => $item['scope'] ?? 'all'];
                }
            }
            return $map;
        };

        $oldMacMap = $toMap($oldMacList);
        $newMacMap = $toMap($newMacList);

        // Remove deleted MACs
        foreach (array_diff_key($oldMacMap, $newMacMap) as $macAddress => $macData) {
            $normalizedMac = $this->normalizeMacAddress($macAddress);
            if ($normalizedMac) {
                \App\Models\Radcheck::where('username', $normalizedMac)
                    ->where('attribute', 'Cleartext-Password')
                    ->where('network_id', $networkId)
                    ->delete();
            }
        }

        // Upsert added / changed MACs
        foreach ($newMacMap as $macAddress => $macData) {
            $normalizedMac = $this->normalizeMacAddress($macAddress);
            if (!$normalizedMac) {
                Log::warning('Invalid MAC address format: ' . $macAddress);
                continue;
            }

            $accessControl = $macData['type'] === 'whitelist' ? 'whitelisted' : 'blacklisted';
            $scope         = $macData['scope'] ?? 'all';

            $isNew        = !isset($oldMacMap[$macAddress]);
            $typeChanged  = !$isNew && $oldMacMap[$macAddress]['type'] !== $macData['type'];
            $scopeChanged = !$isNew && ($oldMacMap[$macAddress]['scope'] ?? 'all') !== $scope;

            if ($isNew || $typeChanged || $scopeChanged) {
                \App\Models\Radcheck::updateOrCreateRecord(
                    $normalizedMac,
                    'Cleartext-Password',
                    $normalizedMac,
                    '==',
                    ['network_id' => $networkId, 'access_control' => $accessControl]
                );
            }
        }
    }

    /**
     * Normalize MAC address to dash-delimited uppercase format
     *
     * @param string $macAddress
     * @return string|null
     */
    private function normalizeMacAddress($macAddress)
    {
        // Remove any existing delimiters and convert to uppercase
        $macAddress = strtoupper(str_replace([':', '-', '.', ' '], '', $macAddress));
        
        // Validate that we have exactly 12 hex characters
        if (strlen($macAddress) !== 12 || !ctype_xdigit($macAddress)) {
            return null;
        }
        
        // Add dash delimiters: XX-XX-XX-XX-XX-XX
        return substr($macAddress, 0, 2) . '-' . 
               substr($macAddress, 2, 2) . '-' . 
               substr($macAddress, 4, 2) . '-' . 
               substr($macAddress, 6, 2) . '-' . 
               substr($macAddress, 8, 2) . '-' . 
               substr($macAddress, 10, 2);
    }

    /**
     * Normalize and validate MAC filter list
     *
     * @param mixed $macFilterList
     * @return array
     */
    private function normalizeMacFilterList($macFilterList)
    {
        if (is_string($macFilterList)) {
            $macFilterList = json_decode($macFilterList, true) ?: [];
        } elseif (!is_array($macFilterList)) {
            $macFilterList = [];
        }
        
        // Handle both old format (array of strings) and new format (array of objects)
        $normalizedMacList = [];
        foreach ($macFilterList as $index => $macItem) {
            if (is_string($macItem)) {
                // Old format - treat as blacklist for backward compatibility, default scope to 'all'
                if (!preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $macItem)) {
                    throw new \Exception('Invalid MAC address format at index ' . $index . ': ' . $macItem);
                }
                $normalizedMacList[] = [
                    'mac' => strtoupper($macItem),
                    'type' => 'blacklist',
                    'scope' => 'all'
                ];
            } elseif (is_array($macItem) && isset($macItem['mac']) && isset($macItem['type'])) {
                // New format - validate MAC address and type
                if (!preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $macItem['mac'])) {
                    throw new \Exception('Invalid MAC address format at index ' . $index . ': ' . $macItem['mac']);
                }
                if (!in_array($macItem['type'], ['whitelist', 'blacklist'])) {
                    throw new \Exception('Invalid MAC address type at index ' . $index . ': ' . $macItem['type'] . '. Must be "whitelist" or "blacklist"');
                }
                
                // Validate and set scope field
                $scope = $macItem['scope'] ?? 'all';
                if (!in_array($scope, ['block_24', 'block_5', 'all'])) {
                    throw new \Exception('Invalid scope value at index ' . $index . ': ' . $scope . '. Must be "block_24", "block_5", or "all"');
                }
                
                $normalizedMacList[] = [
                    'mac' => strtoupper($macItem['mac']),
                    'type' => $macItem['type'],
                    'scope' => $scope
                ];
            } else {
                throw new \Exception('Invalid MAC filter item format at index ' . $index . '. Must be string or object with mac and type properties.');
            }
        }
        
        return $normalizedMacList;
    }

    /**
     * Sync existing MAC addresses to radcheck records for a location
     * This is useful for locations that already have MAC addresses configured
     * but don't have corresponding radcheck records
     *
     * @param int $locationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncMacAddressesToRadcheck($locationId)
    {
        try {
            $location = Location::find($locationId);
            
            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found'
                ], 404);
            }
            
            $settings = LocationSettingsV2::where('location_id', $locationId)->first();
            
            if (!$settings) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location settings not found'
                ], 404);
            }
            
            // MAC filter list is now managed per-network via LocationNetwork
            $macList = [];
            $synced = 0;
            
            foreach ($macList as $macItem) {
                if (is_string($macItem)) {
                    // Old format: just a string MAC address
                    $macAddress = $this->normalizeMacAddress($macItem);
                    $type = 'blacklist'; // Default for old format
                } elseif (is_array($macItem) && isset($macItem['mac']) && isset($macItem['type'])) {
                    // New format
                    $macAddress = $this->normalizeMacAddress($macItem['mac']);
                    $type = $macItem['type'];
                } else {
                    continue; // Skip invalid entries
                }
                
                if ($macAddress) {
                    $accessControl = $type === 'whitelist' ? 'whitelisted' : 'blacklisted';

                    // Scope to the first captive portal network for this location.
                    $captiveNet = \App\Models\LocationNetwork::where('location_id', $locationId)
                        ->where('type', 'captive_portal')
                        ->first();

                    if ($captiveNet) {
                        \App\Models\Radcheck::updateOrCreateRecord(
                            $macAddress,
                            'Cleartext-Password',
                            $macAddress,
                            '==',
                            ['network_id' => $captiveNet->id, 'access_control' => $accessControl]
                        );
                        $synced++;
                    }
                }
            }
            
            // Log::info('Synced MAC addresses to radcheck', [
            //     'location_id' => $locationId,
            //     'synced_count' => $synced
            // ]);
            
            return response()->json([
                'success' => true,
                'message' => "Successfully synced {$synced} MAC addresses to radcheck",
                'data' => [
                    'synced_count' => $synced
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error syncing MAC addresses to radcheck: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error syncing MAC addresses: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create working hours entries for the whole week for a location if they don't exist
     * By default, creates 24/7 access (no time restrictions)
     * 
     * @param int $locationId
     * @return array
     */
    private function createWorkingHoursForWholeWeek($locationId)
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $createdWorkingHours = [];
        
        Log::info("Creating working hours for whole week for location ID: {$locationId}");
        
        foreach ($days as $day) {
            // Check if working hours already exist for this day
            $existingWorkingHour = CaptivePortalWorkingHour::where('location_id', $locationId)
                ->where('day_of_week', $day)
                ->first();
            
            if (!$existingWorkingHour) {
                // Create new working hours entry with null times (24/7 access)
                $workingHour = CaptivePortalWorkingHour::create([
                    'location_id' => $locationId,
                    'day_of_week' => $day,
                    'start_time' => null, // null means 24/7 access
                    'end_time' => null,   // null means 24/7 access
                ]);
                
                $createdWorkingHours[] = $workingHour;
                Log::info("Created working hours for {$day} (24/7 access) for location {$locationId}");
            } else {
                $createdWorkingHours[] = $existingWorkingHour;
                Log::info("Working hours already exist for {$day} for location {$locationId}");
            }
        }
        
        Log::info("Working hours setup completed for location {$locationId}. Total entries: " . count($createdWorkingHours));
        
        return $createdWorkingHours;
    }

    /**
     * Create working hours entries for the whole week with business hours preset
     * Creates Monday-Friday 9AM-5PM, weekends disabled
     * 
     * @param int $locationId
     * @return array
     */
    private function createBusinessWorkingHours($locationId)
    {
        Log::info("Creating business working hours for location ID: {$locationId}");
        
        $workingHoursConfig = [
            'monday' => ['start_time' => '00:00', 'end_time' => '23:59'],
            'tuesday' => ['start_time' => '00:00', 'end_time' => '23:59'],
            'wednesday' => ['start_time' => '00:00', 'end_time' => '23:59'],
            'thursday' => ['start_time' => '00:00', 'end_time' => '23:59'],
            'friday' => ['start_time' => '00:00', 'end_time' => '23:59'],
            'saturday' => ['start_time' => '00:00', 'end_time' => '23:59'],
            'sunday' => ['start_time' => '00:00', 'end_time' => '23:59'],
        ];

        $createdWorkingHours = [];

        Log::info("Creating business working hours for location ID: {$locationId}");

        foreach ($workingHoursConfig as $day => $config) {
            // Use updateOrCreate to handle existing entries
            $workingHour = CaptivePortalWorkingHour::updateOrCreate(
                [
                    'location_id' => $locationId,
                    'day_of_week' => $day,
                ],
                [
                    'start_time' => $config['start_time'],
                    'end_time' => $config['end_time'],
                ]
            );

            $createdWorkingHours[] = $workingHour;

            if ($config['start_time'] && $config['end_time']) {
                Log::info("Set business hours for {$day}: {$config['start_time']}-{$config['end_time']} for location {$locationId}");
            } else {
                Log::info("Disabled {$day} for location {$locationId}");
            }
        }

        // Create hourly schedule records from the working hours
        $this->createHourlyScheduleFromWorkingHours($locationId);

        Log::info("Business working hours setup completed for location {$locationId}");

        return $createdWorkingHours;
    }

    /**
     * Create hourly schedule records from working hours for a location
     * 
     * @param int $locationId
     * @return void
     */
    private function createHourlyScheduleFromWorkingHours($locationId)
    {
        Log::info("Creating hourly schedule from working hours for location ID: {$locationId}");
        
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        
        foreach ($days as $day) {
            for ($hour = 0; $hour < 24; $hour++) {
                // Default to enabled (24/7 access for new locations)
                \App\Models\CaptivePortalHourlySchedule::updateOrCreate(
                    [
                        'location_id' => $locationId,
                        'day_of_week' => $day,
                        'hour' => $hour,
                    ],
                    [
                        'enabled' => true,
                    ]
                );
            }
        }
        
        Log::info("Hourly schedule created for location {$locationId} - all hours enabled (24/7)");
    }

    /**
     * Sort categories for comparison by fetching category names and ordering by name, then ID
     * 
     * @param array $categoryIds Array of category IDs
     * @return array Sorted array of category objects with id and name
     */
    private function sortCategoriesForComparison($categoryIds)
    {
        if (empty($categoryIds) || !is_array($categoryIds)) {
            return [];
        }

        // Fetch categories with their names from the database
        $categories = \App\Models\Category::whereIn('id', $categoryIds)
            ->select('id', 'name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => (string)$category->id, // Ensure ID is string for consistent comparison
                    'name' => $category->name
                ];
            })
            ->toArray();

        // Sort by name first, then by ID for consistent ordering
        usort($categories, function ($a, $b) {
            $nameComparison = strcmp($a['name'], $b['name']);
            if ($nameComparison === 0) {
                return strcmp($a['id'], $b['id']);
            }
            return $nameComparison;
        });

        return $categories;
    }
}