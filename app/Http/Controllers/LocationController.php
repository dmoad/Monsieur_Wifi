<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Device;
use App\Models\LocationSettings;
use App\Models\SystemSetting;
use App\Models\Radacct;
use App\Models\Radcheck;
use App\Models\Firmware;
use App\Models\OnlineNetworkUser;
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
        // Get locations with their associated devices
        $locations = Location::with('device')->get();
        
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
            $locationData['settings'] = LocationSettings::where('location_id', $location->id)->first();
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
            'mac_address' => 'required|string|max:255|unique:devices,mac_address',
            'model' => 'required|string|in:820AX,835AX',
            'serial_number' => 'nullable|string|max:255|unique:devices,serial_number',
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

        // Create a new device
        $device = new Device();
        // $mac_address = strtoupper($request->mac_address);
        $mac_address = str_replace(':', '-', $request->mac_address);
        $device->name = $request->mac_address;
        $device->mac_address = $mac_address;
        $device->model = $request->model; // Set the device model
        $device->configuration_version = 1;
        if ($request->serial_number) {
            $device->serial_number = $request->serial_number;
        } else {
            $device->serial_number = Str::random(20);
        }
        $device->device_key = Str::random(20);
        $device->device_secret = Str::random(30);
        $device->save();

                // Try to get the default firmware for the device model
        $firmware = Firmware::getDefaultForModel($device->model);
        
        // If no default firmware found, get the latest enabled firmware for the model
        if (!$firmware) {
            $firmware = Firmware::forModel($device->model)->enabled()->orderBy('created_at', 'desc')->first();
        }
        
        // If still no firmware found, get the latest firmware for the model (even if disabled)
        if (!$firmware) {
            $firmware = Firmware::forModel($device->model)->orderBy('created_at', 'desc')->first();
        }
        
        // Set firmware_id - either the found firmware ID or 0 if no firmware found
        $device->firmware_id = $firmware ? $firmware->id : 0;
        $device->save();

        // Create the location with the device
        $location = new Location($request->except(['mac_address', 'device_name', 'serial_number']));
        $location->device_id = $device->id;
        $location->user_id = Auth::id();
        $location->save();

        // Create the location settings
        $locationSettings = new LocationSettings();
        $locationSettings->location_id = $location->id;
        // Get values from system settings and set default values
        $systemSettings = SystemSetting::first();
        $locationSettings->captive_portal_enabled = true;
        $locationSettings->wifi_name = $systemSettings->default_essid;
        $locationSettings->wifi_password = $systemSettings->default_password;
        $locationSettings->captive_portal_ssid = 'Mr WiFi Guest';
        $locationSettings->wifi_security_type = 'WPA2';
        // $locationSettings->wifi_channel = 1;
        // $locationSettings->wifi_bandwidth = '20MHz';
        // $locationSettings->wifi_channel_width = '20MHz';
        // $locationSettings->wifi_channel_width_5g = '20MHz';
        
        
        $locationSettings->captive_portal_visible = true;
        $locationSettings->captive_portal_enabled = true;
        $locationSettings->welcome_message = 'Welcome to MrWiFi Network';
        $locationSettings->save();

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
     * Display the specified location.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $location = Location::find($id);
        
        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found'
            ], 404);
        }
        
        $device = Device::find($location->device_id);
        $locationSettings = LocationSettings::where('location_id', $id)->first();
        
        $locationData = $location->toArray();
        $locationData['settings'] = $locationSettings;
        $locationData['device'] = $device;
        
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
            $period = $request->get('period', 7);
            
            // Calculate date range
            $endDate = Carbon::now();
            $startDate = Carbon::now()->subDays($period - 1);
            
            // Get daily statistics from Radacct
            $dailyStats = Radacct::getSessionStats($id, $startDate, $endDate);
            
            // Transform the data for the chart
            $chartData = [];
            foreach ($dailyStats as $date => $stats) {
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
                $locationSettings = LocationSettings::where('location_id', $location_id)->first();
                
                // If settings don't exist, create them
                if (!$locationSettings) {
                    $locationSettings = new LocationSettings();
                    $locationSettings->location_id = $location_id;
                    
                    // Set required default values to prevent DB errors
                    $locationSettings->welcome_message = 'Welcome to MrWiFi Network';
                    $locationSettings->wifi_name = 'MrWiFi Network';
                    $locationSettings->wifi_password = 'password123';
                    $locationSettings->captive_portal_ssid = 'MrWiFi Guest';
                    $locationSettings->wifi_security_type = 'WPA2';
                    $locationSettings->captive_portal_visible = true;
                    $locationSettings->captive_portal_enabled = true;
                }
                
                // Handle different types of settings
                if ($settingsType === 'captive' || $settingsType === 'captive_portal') {
                    Log::info('=== CAPTIVE PORTAL SETTINGS UPDATE ===');
                    Log::info('Settings type: ' . $settingsType);
                    Log::info('Location ID: ' . $location_id);
                    Log::info('Settings data: ' . json_encode($settings));
                    
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
                    
                    // Bandwidth settings
                    if (isset($settings['captive_download_limit'])) {
                        if ($settings['captive_download_limit'] !== $locationSettings->download_limit) {
                            # $increment_version = 1;
                            Log::info('Bandwidth limit up updated');
                        }
                        $locationSettings->download_limit = $settings['captive_download_limit'];
                    }
                    
                    if (isset($settings['captive_upload_limit'])) {
                        if ($settings['captive_upload_limit'] !== $locationSettings->upload_limit) {
                            # $increment_version = 1;
                            Log::info('Bandwidth limit down updated');
                        }
                        $locationSettings->upload_limit = $settings['captive_upload_limit'];
                    }
                    
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
                    // Update WAN settings
                    if (isset($settings['wan_connection_type'])) {
                        if ($settings['wan_connection_type'] !== $locationSettings->wan_connection_type) {
                            $increment_version = 1;
                            Log::info('WAN connection type updated');
                        }
                        $locationSettings->wan_connection_type = $settings['wan_connection_type'];
                    }

                    if (isset($settings['wan_enabled'])) {
                        if ($settings['wan_enabled'] !== $locationSettings->wan_enabled) {
                            $increment_version = 1;
                            Log::info('WAN enabled status updated');
                        }
                        $locationSettings->wan_enabled = $settings['wan_enabled'];
                    }

                    if (isset($settings['wan_nat_enabled'])) {
                        if ($settings['wan_nat_enabled'] !== $locationSettings->wan_nat_enabled) {
                            $increment_version = 1;
                            Log::info('WAN NAT status updated');
                        }
                        $locationSettings->wan_nat_enabled = $settings['wan_nat_enabled'];
                    }

                    // Static IP settings
                    if ($settings['wan_connection_type'] === 'static') {
                        if (isset($settings['wan_ip_address'])) {
                            if ($settings['wan_ip_address'] !== $locationSettings->wan_ip_address) {
                                $increment_version = 1;
                                Log::info('WAN IP address updated');
                            }
                            $locationSettings->wan_ip_address = $settings['wan_ip_address'];
                        }

                        if (isset($settings['wan_netmask'])) {
                            if ($settings['wan_netmask'] !== $locationSettings->wan_netmask) {
                                $increment_version = 1;
                                Log::info('WAN netmask updated');
                            }
                            $locationSettings->wan_netmask = $settings['wan_netmask'];
                        }

                        if (isset($settings['wan_gateway'])) {
                            if ($settings['wan_gateway'] !== $locationSettings->wan_gateway) {
                                $increment_version = 1;
                                Log::info('WAN gateway updated');
                            }
                            $locationSettings->wan_gateway = $settings['wan_gateway'];
                        }

                        if (isset($settings['wan_primary_dns'])) {
                            if ($settings['wan_primary_dns'] !== $locationSettings->wan_primary_dns) {
                                $increment_version = 1;
                                Log::info('WAN primary DNS updated');
                            }
                            $locationSettings->wan_primary_dns = $settings['wan_primary_dns'];
                        }

                        if (isset($settings['wan_secondary_dns'])) {
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
                Log::info('=== SAVING LOCATION SETTINGS ===');
                Log::info('Location settings before save: ' . json_encode($locationSettings->toArray()));
                $locationSettings->save();
                Log::info('Location settings saved successfully');
                Log::info('Location settings after save: ' . json_encode($locationSettings->fresh()->toArray()));
                
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
                if (isset($deviceData['model'])) {
                    $newModel = $deviceData['model'];
                    $oldModel = $device->model;
                    
                    // Validate model (only allow 820AX and 835AX)
                    if (!in_array($newModel, ['820AX', '835AX', ''])) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid device model. Only 820AX and 835AX are supported.'
                        ], 400);
                    }
                    
                    Log::info("Updating device model from '{$oldModel}' to '{$newModel}' for device: " . $device->id);
                    
                    // Update the model
                    $device->model = $newModel;
                    
                    // If model changed, increment configuration version
                    if ($oldModel !== $newModel) {
                        $device->configuration_version = $device->configuration_version + 1;
                        Log::info('Device configuration version incremented to: ' . $device->configuration_version);
                    }
                    
                    $device->save();
                    
                    Log::info('Device model updated successfully');
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Device model updated successfully',
                        'data' => [
                            'device' => $device,
                            'old_model' => $oldModel,
                            'new_model' => $newModel
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
        Log::info('Update general location information received');
        Log::info($request->all());
        
        try {
            $location = Location::find($location_id);
            
            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found'
                ], 404);
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
            ]);
            Log::info('Validated data: ');
            Log::info($validated);
            
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
            
            // Update the location with validated data
            $location->update($validated);
            
            // Get associated data
            $device = Device::find($location->device_id);
            $locationSettings = LocationSettings::where('location_id', $location_id)->first();
            
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
        Log::info('Update firmware request received for location: ' . $id);
        Log::info($request->all());
        
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
            $deviceModelCompatible = false;
            if ($firmware->model == '1' || $firmware->model == '820AX') {
                $deviceModelCompatible = ($device->model == '820AX' || $device->model == '1');
            } elseif ($firmware->model == '2' || $firmware->model == '835AX') {
                $deviceModelCompatible = ($device->model == '835AX' || $device->model == '2');
            }
            
            if (!$deviceModelCompatible) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firmware is not compatible with device model'
                ], 400);
            }
            
            // Update device firmware version
            $device->firmware_version = $request->firmware_version ?: $firmware->name;
            $device->firmware_id = $firmware->id;
            
            // Increment configuration version to trigger device update
            $device->configuration_version = $device->configuration_version + 1;
            
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
            
            $settings = LocationSettings::where('location_id', $id)->first();
            
            if (!$settings) {
                // Create default settings if none exist
                $settings = new LocationSettings([
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
        Log::info('Update location settings request received for location: ' . $id);
        Log::info($request->all());
        
        try {
            $location = Location::find($id);
            
            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found'
                ], 404);
            }
            
            $settings = LocationSettings::where('location_id', $id)->first();
            
            if (!$settings) {
                // Create new settings if none exist
                $settings = new LocationSettings(['location_id' => $id]);
            }
            
            // Get the device for config version increment
            $device = Device::find($location->device_id);
            $increment_version = 0;
            
            // Store original values for comparison
            $originalSettings = [
                'country_code' => $settings->country_code,
                'transmit_power_2g' => $settings->transmit_power_2g,
                'transmit_power_5g' => $settings->transmit_power_5g,
                'channel_2g' => $settings->channel_2g,
                'channel_5g' => $settings->channel_5g,
                'channel_width_2g' => $settings->channel_width_2g,
                'channel_width_5g' => $settings->channel_width_5g,
                'password_wifi_vlan' => $settings->password_wifi_vlan,
                'captive_portal_vlan' => $settings->captive_portal_vlan,
                'captive_portal_vlan_tagging' => $settings->captive_portal_vlan_tagging,
                'password_wifi_vlan_tagging' => $settings->password_wifi_vlan_tagging,
                'vlan_enabled' => $settings->vlan_enabled,
            ];
            
            // Update settings with provided data
            $settingsData = $request->only([
                'web_filter_enabled',
                'web_filter_categories',
                'web_filter_domains',
                // Add other settings fields as needed
                'wifi_name',
                'wifi_password',
                'wifi_visible',
                'wifi_security_type',
                'captive_portal_enabled',
                'captive_portal_ssid',
                'captive_portal_visible',
                'captive_auth_method',
                'captive_portal_password',
                'session_timeout',
                'idle_timeout',
                'bandwidth_limit',
                'download_limit',
                'upload_limit',
                'country_code',
                'transmit_power_2g',
                'transmit_power_5g',
                'channel_2g',
                'channel_5g',
                'channel_width_2g',
                'channel_width_5g',
                'mac_filter_list',
                'captive_mac_filter_list',
                'secured_mac_filter_list',
                // New VLAN and redirect fields
                'password_wifi_vlan',
                'captive_portal_vlan',
                'captive_portal_redirect',
                'captive_portal_vlan_tagging',
                'password_wifi_vlan_tagging',
                'vlan_enabled',
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
            
            // VLAN changes
            if (isset($settingsData['password_wifi_vlan']) && $settingsData['password_wifi_vlan'] !== $originalSettings['password_wifi_vlan']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('Password WiFi VLAN updated from "' . $originalSettings['password_wifi_vlan'] . '" to "' . $settingsData['password_wifi_vlan'] . '"');
            }
            
            if (isset($settingsData['captive_portal_vlan']) && $settingsData['captive_portal_vlan'] !== $originalSettings['captive_portal_vlan']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('Captive portal VLAN updated from "' . $originalSettings['captive_portal_vlan'] . '" to "' . $settingsData['captive_portal_vlan'] . '"');
            }
            
            // VLAN tagging changes
            if (isset($settingsData['password_wifi_vlan_tagging']) && $settingsData['password_wifi_vlan_tagging'] !== $originalSettings['password_wifi_vlan_tagging']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('Password WiFi VLAN tagging updated from "' . $originalSettings['password_wifi_vlan_tagging'] . '" to "' . $settingsData['password_wifi_vlan_tagging'] . '"');
            }
            
            if (isset($settingsData['captive_portal_vlan_tagging']) && $settingsData['captive_portal_vlan_tagging'] !== $originalSettings['captive_portal_vlan_tagging']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('Captive portal VLAN tagging updated from "' . $originalSettings['captive_portal_vlan_tagging'] . '" to "' . $settingsData['captive_portal_vlan_tagging'] . '"');
            }
            
            // Global VLAN enable/disable changes
            if (isset($settingsData['vlan_enabled']) && $settingsData['vlan_enabled'] !== $originalSettings['vlan_enabled']) {
                $increment_version = 1;
                $routerSettingsChanged = true;
                Log::info('VLAN enabled updated from "' . ($originalSettings['vlan_enabled'] ? 'true' : 'false') . '" to "' . ($settingsData['vlan_enabled'] ? 'true' : 'false') . '"');
            }
            
            // Ensure web_filter_categories is properly handled as JSON
            if (isset($settingsData['web_filter_categories'])) {
                if (is_string($settingsData['web_filter_categories'])) {
                    $settingsData['web_filter_categories'] = json_decode($settingsData['web_filter_categories'], true) ?: [];
                } elseif (!is_array($settingsData['web_filter_categories'])) {
                    $settingsData['web_filter_categories'] = [];
                }
            }
            

            
            // Handle Captive Portal MAC filter list (adds to radcheck, no config version increment)
            if (isset($settingsData['captive_mac_filter_list'])) {
                $normalizedMacList = $this->normalizeMacFilterList($settingsData['captive_mac_filter_list']);
                
                // Check if MAC filter list changed
                $currentMacList = $settings->captive_mac_filter_list ?: [];
                if (json_encode($normalizedMacList) !== json_encode($currentMacList)) {
                    Log::info('Captive portal MAC filter list updated (no config version increment)', [
                        'old_list' => $currentMacList,
                        'new_list' => $normalizedMacList
                    ]);
                    
                    // Update the captive portal MAC filter list first
                    $settings->captive_mac_filter_list = $normalizedMacList;
                    
                    // Handle radcheck records for captive portal MAC address filtering
                    $this->updateRadcheckForMacFiltering($settings, $currentMacList, $normalizedMacList);
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
                    Log::info('Secured WiFi MAC filter list updated (config version increment)', [
                        'old_list' => $currentMacList,
                        'new_list' => $normalizedMacList
                    ]);
                    
                    // Update the secured WiFi MAC filter list (no radcheck records for secured WiFi)
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
                    Log::info('Legacy MAC filter list updated (treated as captive portal)', [
                        'old_list' => $currentMacList,
                        'new_list' => $normalizedMacList
                    ]);
                    
                    // Update the MAC filter list and handle radcheck records
                    $settings->mac_filter_list = $normalizedMacList;
                    $this->updateRadcheckForMacFiltering($settings, $currentMacList, $normalizedMacList);
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
            Log::info('Updating MAC address for location: ' . $id);
            
            // Validate request
            $request->validate([
                'mac_address' => 'required|string|regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/',
            ]);

            // Find the location
            $location = Location::find($id);
            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found'
                ], 404);
            }

            // Find the device associated with this location
            $device = $location->device;
            if (!$device) {
                return response()->json([
                    'success' => false,
                    'message' => 'No device associated with this location'
                ], 404);
            }

            $newMacAddress = str_replace(':', '-', $request->mac_address);
            
            // Check if MAC address is already in use by another device
            $existingDevice = Device::where('mac_address', $newMacAddress)
                                  ->where('id', '!=', $device->id)
                                  ->first();
            
            if ($existingDevice) {
                return response()->json([
                    'success' => false,
                    'message' => 'MAC address is already in use by another device'
                ], 409);
            }

            // Update the MAC address
            $oldMacAddress = $device->mac_address;
            $device->mac_address = $newMacAddress;
            $device->save();

            Log::info('MAC address updated successfully', [
                'location_id' => $id,
                'device_id' => $device->id,
                'old_mac_address' => $oldMacAddress,
                'new_mac_address' => $newMacAddress
            ]);

            return response()->json([
                'success' => true,
                'message' => 'MAC address updated successfully',
                'data' => [
                    'device' => $device,
                    'old_mac_address' => $oldMacAddress,
                    'new_mac_address' => $newMacAddress
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid MAC address format. Please use format XX-XX-XX-XX-XX-XX',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating MAC address: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating MAC address: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update radcheck records for MAC address filtering
     *
     * @param \App\Models\LocationSettings $settings
     * @param array $oldMacList
     * @param array $newMacList
     * @return void
     */
    private function updateRadcheckForMacFiltering($settings, $oldMacList, $newMacList)
    {
        Log::info('Updating radcheck records for MAC filtering', [
            'location_id' => $settings->location_id,
            'old_count' => count($oldMacList),
            'new_count' => count($newMacList)
        ]);

        // Convert arrays to associative arrays for easier comparison
        $oldMacMap = [];
        foreach ($oldMacList as $macItem) {
            if (is_string($macItem)) {
                $oldMacMap[$macItem] = 'blacklist';
            } elseif (is_array($macItem) && isset($macItem['mac']) && isset($macItem['type'])) {
                $oldMacMap[$macItem['mac']] = $macItem['type'];
            }
        }

        $newMacMap = [];
        foreach ($newMacList as $macItem) {
            if (is_string($macItem)) {
                $newMacMap[$macItem] = 'blacklist';
            } elseif (is_array($macItem) && isset($macItem['mac']) && isset($macItem['type'])) {
                $newMacMap[$macItem['mac']] = $macItem['type'];
            }
        }

        // Find MAC addresses that were removed
        $removedMacs = array_diff_key($oldMacMap, $newMacMap);
        foreach ($removedMacs as $macAddress => $type) {
            Log::info('Removing MAC address from radcheck: ' . $macAddress);
            
            // Normalize MAC address to dash format
            $normalizedMac = $this->normalizeMacAddress($macAddress);
            if ($normalizedMac) {
                \App\Models\Radcheck::where('username', $normalizedMac)
                    ->where('attribute', 'Cleartext-Password')
                    ->where('location_id', $settings->location_id)
                    ->delete();
            }
        }

        // Find MAC addresses that were added or changed
        foreach ($newMacMap as $macAddress => $type) {
            $normalizedMac = $this->normalizeMacAddress($macAddress);
            if (!$normalizedMac) {
                Log::warning('Invalid MAC address format: ' . $macAddress);
                continue;
            }

            $accessControl = $type === 'whitelist' ? 'whitelisted' : 'blacklisted';
            
            // Check if this is a new MAC address or if the type changed
            if (!isset($oldMacMap[$macAddress]) || $oldMacMap[$macAddress] !== $type) {
                Log::info('Adding/updating MAC address in radcheck: ' . $normalizedMac . ' as ' . $accessControl);
                
                \App\Models\Radcheck::updateOrCreateRecord(
                    $normalizedMac,
                    'Cleartext-Password',
                    $normalizedMac,
                    '==',
                    [
                        'location_id' => $settings->location_id,
                        'access_control' => $accessControl
                    ]
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
                // Old format - treat as blacklist for backward compatibility
                if (!preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $macItem)) {
                    throw new \Exception('Invalid MAC address format at index ' . $index . ': ' . $macItem);
                }
                $normalizedMacList[] = [
                    'mac' => strtoupper($macItem),
                    'type' => 'blacklist'
                ];
            } elseif (is_array($macItem) && isset($macItem['mac']) && isset($macItem['type'])) {
                // New format - validate MAC address and type
                if (!preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $macItem['mac'])) {
                    throw new \Exception('Invalid MAC address format at index ' . $index . ': ' . $macItem['mac']);
                }
                if (!in_array($macItem['type'], ['whitelist', 'blacklist'])) {
                    throw new \Exception('Invalid MAC address type at index ' . $index . ': ' . $macItem['type'] . '. Must be "whitelist" or "blacklist"');
                }
                $normalizedMacList[] = [
                    'mac' => strtoupper($macItem['mac']),
                    'type' => $macItem['type']
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
            
            $settings = LocationSettings::where('location_id', $locationId)->first();
            
            if (!$settings) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location settings not found'
                ], 404);
            }
            
            $macList = $settings->mac_filter_list ?: [];
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
                    
                    \App\Models\Radcheck::updateOrCreateRecord(
                        $macAddress,
                        'Cleartext-Password',
                        $macAddress,
                        '==',
                        [
                            'location_id' => $locationId,
                            'access_control' => $accessControl
                        ]
                    );
                    
                    $synced++;
                }
            }
            
            Log::info('Synced MAC addresses to radcheck', [
                'location_id' => $locationId,
                'synced_count' => $synced
            ]);
            
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
}