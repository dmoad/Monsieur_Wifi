<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Location;
use App\Models\SystemSetting;
use App\Models\LocationSettings;
use App\Models\ScanResult;
use App\Models\OnlineNetworkUser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Firmware;
use App\Models\Category;
use App\Models\BlockedDomain;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\CaptivePortalWorkingHour;

class DeviceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Remove all middleware assignments from here
        // Middleware will be defined in routes files instead
    }

    /**
     * Display a listing of the devices.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $devices = Device::all();
        return view('devices.index', compact('devices'));
    }

    /**
     * Show the form for creating a new device.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('devices.create');
    }

    /**
     * Store a newly created device in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'required|string|max:255|unique:devices',
            'mac_address' => 'required|string|max:255|unique:devices',
            'firmware_version' => 'nullable|string|max:255',
        ]);

        $device = new Device($request->all());
        
        // Generate device key and secret
        $device->device_key = Str::random(32);
        $device->device_secret = Str::random(64);
        
        $device->save();

        // Try to get the default firmware for the device model
        $firmware = Firmware::getDefaultForModel($device->model);
        
        // If no default firmware found, get the latest firmware for the model
        if (!$firmware) {
            $firmware = Firmware::forModel($device->model)->enabled()->orderBy('created_at', 'desc')->first();
        }
        
        // If still no firmware found, just get the latest firmware for the model (even if disabled)
        if (!$firmware) {
            $firmware = Firmware::forModel($device->model)->orderBy('created_at', 'desc')->first();
        }
        
        // Set firmware_id - either the found firmware ID or 0 if no firmware found
        $device->firmware_id = $firmware ? $firmware->id : 0;
        $device->save();

        return redirect()->route('devices.index')
            ->with('success', 'Device created successfully.');
    }

    /**
     * Display the specified device.
     *
     * @param  \App\Models\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function show(Device $device)
    {
        return view('devices.show', compact('device'));
    }

    /**
     * Show the form for editing the specified device.
     *
     * @param  \App\Models\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function edit(Device $device)
    {
        return view('devices.edit', compact('device'));
    }

    /**
     * Update the specified device in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Device $device)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'required|string|max:255|unique:devices,serial_number,' . $device->id,
            'mac_address' => 'required|string|max:255|unique:devices,mac_address,' . $device->id,
            'firmware_version' => 'nullable|string|max:255',
        ]);

        $device->update($request->all());

        return redirect()->route('devices.index')
            ->with('success', 'Device updated successfully');
    }

    /**
     * Remove the specified device from storage.
     *
     * @param  \App\Models\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function destroy(Device $device)
    {
        $device->delete();

        return redirect()->route('devices.index')
            ->with('success', 'Device deleted successfully');
    }

    public function getSettings($device_key, $device_secret)
    {
        $device = Device::where('device_key', $device_key)->where('device_secret', $device_secret)->first();
        if (!$device) {
            return response()->json(['error' => 'Invalid device credentials'], 401);
        }

        $location = Location::where('device_id', $device->id)->first();
        if (!$location) {
            return response()->json(['error' => 'Location not found'], 404);
        }

        $settings = LocationSettings::where('location_id', $location->id)->first();
        if (!$settings) {
            return response()->json(['error' => 'Settings not found'], 404);
        }

        $settings->wifi_name = $settings->password_wifi_ssid;
        $settings->wifi_password = $settings->password_wifi_password;
        
        // Check if web filtering is enabled
        if (!$settings->web_filter_enabled || empty($settings->web_filter_categories)) {
            // Web filtering is disabled or no categories selected, return empty array
            $domain_blocked = collect();
        } else {
            // Web filtering is enabled, get domains for enabled categories
            // Filter out categories which are not enabled
            $enabled_categories = Category::whereIn('id', $settings->web_filter_categories)->where('is_enabled', true)->pluck('id');
            $settings->web_filter_categories = $enabled_categories;
            $domain_blocked = BlockedDomain::select('domain')->whereIn('category_id', $settings->web_filter_categories)->get();
        }
        // Remove duplicate domains
        $domain_blocked = $domain_blocked->unique('domain');
        // Remove domains that are empty
        $domain_blocked = $domain_blocked->filter(function($domain) {
            return !empty($domain->domain);
        });
        // Clean up the settings object - remove internal fields
        unset($settings->web_filter_categories);
        $settings->blocked_domains = $domain_blocked;

        $system_settings = SystemSetting::first();
        $radius_settings = [
            'radius_ip' => $system_settings->radius_ip,
            'radius_port' => $system_settings->radius_port,
            'radius_secret' => $system_settings->radius_secret,
            'accounting_port' => $system_settings->accounting_port,
        ];

        $whitelist_domains = env('GUEST_WHITELIST_DOMAINS');
        $whitelist_servers = env('GUEST_WHITELIST_SERVERS');
        // if settings.captive_auth_method is set to social and captive_social_auth_method is set to twitter then return whitelist_domains as ['twitter.com']
        if ($settings->captive_auth_method == 'social' && $settings->captive_social_auth_method == 'google') {
            $whitelist_domains = $whitelist_domains . ',' . env('GOOGLE_WHITELIST_DOMAINS');
            $whitelist_servers = $whitelist_servers . ',' . env('GOOGLE_WHITELIST_SERVERS');
        }

        if ($settings->captive_auth_method == 'social' && $settings->captive_social_auth_method == 'facebook') {
            $whitelist_domains = $whitelist_domains . ',' . env('FACEBOOK_WHITELIST_DOMAINS');
            $whitelist_servers = $whitelist_servers . ',' . env('FACEBOOK_WHITELIST_SERVERS');
        }

        $whitelist_domains = rtrim($whitelist_domains, ',');
        $whitelist_servers = rtrim($whitelist_servers, ',');
        
        $guest_settings = [
            'login_url' => env('GUEST_LOGIN_URL'),
            'whitelist_servers' => $whitelist_servers,
            'whitelist_domains' => $whitelist_domains,
        ];

        $firmware_version = $device->firmware_id;
        Log::info('Firmware version: ' . $firmware_version);
        Log::info('Device: ');
        Log::info($device);
        
        // Only try to get firmware info if firmware_id is not 0
        $firmware_info = null;
        if ($firmware_version && $firmware_version > 0) {
            $firmware_info = Firmware::where('id', $firmware_version)->first();
        }

        Log::info('Firmware info: ');
        Log::info($firmware_info);

        if (!$firmware_info) {
            $firmware_version = 0;
        }

        // Handle firmware file information
        $file_name = null;
        $firmware_url = null;
        $firmware_hash = null;

        if ($firmware_info) {
            $file_name = $firmware_info->file_path;
            // remove the first part of the file_path after the last /
            $file_name = substr($file_name, strrpos($file_name, '/') + 1);
            
            // Generate full URL for firmware file
            $firmware_url = Storage::disk('public')->url($firmware_info->file_path);
            $firmware_hash = $firmware_info->md5sum;
        }

        $firmware = [
            'version' => $firmware_version,
            'file_path' => $firmware_url,
            'file_name' => $file_name,
            'hash' => $firmware_hash,
        ];

        return response()->json(
            [
                'status' => 'success',
                'location' => $location,
                'settings' => $settings,
                'radius_settings' => $radius_settings,
                'guest_settings' => $guest_settings,
                'firmware' => $firmware
            ]
        );
    }

    public function heartbeat($device_key, $device_secret, Request $request)
    {
        Log::info('Heartbeat request: '.$device_key.' '.$device_secret);
        Log::info($request->all());

        $firmware_version = $request->input('firmware_version');
        $firmware_id = $request->input('firmware_id');

        if ($firmware_version) {
            $firmware = Firmware::where('id', $firmware_version)->first();
        }

        $device = Device::where('device_key', $device_key)->where('device_secret', $device_secret)->first();
        if (!$device) {
            return response()->json(['error' => 'Invalid device credentials'], 401);
        }

        // Update the last_seen field
        $device->last_seen = now();
        // if uptime in request is not set or is null or not integer, set it to 0
        if (!$request->input('uptime') || !is_numeric($request->input('uptime'))) {
            Log::info('Uptime is not set or is not an integer, setting to 0');
            $uptime = 0;
        } else {
            $uptime = $request->input('uptime');
        }
        $device->uptime = $uptime;

        $device->save();

        // If device firmware_id is null return the defaukt firmware, else return firmware_id

        if($device->firmware_id == null) {

            // Try to get the default firmware for the device model first
            $firmware = Firmware::getDefaultForModel($device->model);

            // If no default firmware found, get the latest enabled firmware for the model
            if (!$firmware) {
                $firmware = Firmware::forModel($device->model)->enabled()->orderBy('created_at', 'desc')->first();
            }

            // If still no firmware found, get the latest firmware for the model (even if disabled)
            if (!$firmware) {
                $firmware = Firmware::forModel($device->model)->orderBy('created_at', 'desc')->first();
            }
 
            $firmware_version = $firmware ? $firmware->id : 0;
        } else {
            $firmware_version = $device->firmware_id;
        }

        $location = Location::where('device_id', $device->id)->first();
        if (!$location) {
            return response()->json(['error' => 'Location not found'], 404);
        }

        $settings = LocationSettings::where('location_id', $location->id)->first();

        // Check if captive portal should be enabled based on working hours
        $captivePortalEnabled = $this->isCaptivePortalEnabledAtCurrentTime($location->id);
        // convert to 1/0
        $captivePortalEnabled = $captivePortalEnabled ? 1 : 0;

        return response()->json([
            'status' => 'success', 
            'config_version' => $device->configuration_version, 
            'reboot_count' => $device->reboot_count, 
            'firmware_version' => $firmware_version, 
            'scan_counter' => $device->scan_counter,
            'captive_portal_enabled' => $captivePortalEnabled
        ]);
    }

    /**
     * Check if captive portal should be enabled based on current time and working hours
     */
    private function isCaptivePortalEnabledAtCurrentTime($locationId)
    {
        // Get current day of week and hour
        $currentDayOfWeek = strtolower(now()->format('l')); // monday, tuesday, etc.
        $currentHour = (int) now()->format('H'); // 0-23
        $currentTime = now()->format('H:i');
        
        Log::info("Checking captive portal hourly schedule for location {$locationId}: {$currentDayOfWeek} hour {$currentHour} ({$currentTime})");
        
        // Get hourly schedule for the current hour
        $hourlySchedule = \App\Models\CaptivePortalHourlySchedule::where('location_id', $locationId)
            ->where('day_of_week', $currentDayOfWeek)
            ->where('hour', $currentHour)
            ->first();
        
        if ($hourlySchedule) {
            // If hourly schedule exists, use its enabled status
            Log::info("Hourly schedule found for {$currentDayOfWeek} hour {$currentHour}: " . ($hourlySchedule->enabled ? 'enabled' : 'disabled'));
            return $hourlySchedule->enabled;
        }
        
        // No hourly schedule found - return false (disabled)
        Log::info("No hourly schedule found for {$currentDayOfWeek} hour {$currentHour} - captive portal disabled");
        return false;
    }

    public function verify($mac_address, $verification_code)
    {
        if ($verification_code !== env('VERIFICATION_CODE')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized verification code'
            ], 401);
        }
        $mac_address = strtoupper($mac_address);
        // Replace : with -
        $mac_address = str_replace(':', '-', $mac_address);
        $device = Device::select('id', 'mac_address', 'device_key', 'device_secret', 'configuration_version')->where('mac_address', $mac_address)->first();
        if (!$device) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized device'
            ], 401);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Device verified successfully',
            'device' => $device
        ]);
    }

    public function updateClientList($device_key, $device_secret, Request $request)
    {
        Log::info('Update client list request: ');
        Log::info($device_key.' '.$device_secret);
        Log::info($request->all());
        
        $device = Device::where('device_key', $device_key)->where('device_secret', $device_secret)->first();
        if (!$device) {
            return response()->json(['error' => 'Invalid device credentials'], 401);
        }

        // Validate the request
        $request->validate([
            'timestamp' => 'required|string',
            'clients' => 'required|array',
            'clients.*.mac' => 'required|string',
            'clients.*.type' => 'required|string',
            'clients.*.ip' => 'required|string',
            'clients.*.interface' => 'required|string',
            'clients.*.hostname' => 'nullable|string',
            'clients.*.network' => 'required|string',
            'summary' => 'required|array',
            'synced' => 'required|boolean'
        ]);

        $location = Location::where('device_id', $device->id)->first();
        if (!$location) {
            return response()->json(['error' => 'Location not found'], 404);
        }

        $location_id = $location->id;
        Log::info('Location ID: '.$location_id);
        
        // Delete all existing online network users for this location
        OnlineNetworkUser::where('location_id', $location_id)->delete();
        
        // Create new records for all clients in the payload
        $clients = $request->input('clients');
        foreach ($clients as $client) {
            OnlineNetworkUser::create([
                'mac' => $client['mac'],
                'type' => $client['type'],
                'ip' => $client['ip'],
                'interface' => $client['interface'],
                'hostname' => $client['hostname'] ?? '',
                'network' => $client['network'],
                'location_id' => $location_id,
            ]);
        }

        Log::info('Client list updated', [
            'device_key' => $device_key,
            'location_id' => $location_id,
            'clients_count' => count($clients),
            'timestamp' => $request->input('timestamp')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Client list updated successfully',
            'clients_processed' => count($clients),
            'location_id' => $location_id,
            'timestamp' => $request->input('timestamp')
        ]);
    }

    /**
     * Reboot a device and increment the reboot count.
     *
     * @param  \App\Models\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function reboot(Device $device)
    {
        try {
            // Increment the reboot count
            $device->increment('reboot_count');
            
            // Update last_seen to current timestamp
            $device->last_seen = now();
            $device->save();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Device restart initiated successfully',
                'reboot_count' => $device->reboot_count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to restart device: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Initiate a channel scan for a device
     */
    public function initiateScan(Request $request, $locationId)
    {
        try {
            // Find the location and its associated device
            $location = Location::findOrFail($locationId);
            $device = $location->device;
            
            if (!$device) {
                return response()->json([
                    'message' => 'No device found for this location'
                ], 404);
            }

            // Increment the scan counter
            $scanId = $device->incrementScanCounter();

            // Create a new scan result entry
            $scanResult = ScanResult::create([
                'device_id' => $device->id,
                'scan_id' => $scanId,
                'status' => ScanResult::STATUS_INITIATED,
            ]);

            // Here you would typically send a command to the device to start scanning
            // For now, we'll just return the scan result
            
            return response()->json([
                'message' => 'Channel scan initiated successfully',
                'data' => [
                    'scan_id' => $scanId,
                    'scan_result_id' => $scanResult->id,
                    'status' => $scanResult->status,
                    'device_id' => $device->id,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to initiate scan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the status of a scan
     */
    public function getScanStatus($locationId, $scanId)
    {
        Log::info('getScanStatus called with locationId: ' . $locationId . ', scanId: ' . $scanId); 
        try {
            $location = Location::findOrFail($locationId);
            $device = $location->device;
            
            if (!$device) {
                return response()->json([
                    'message' => 'No device found for this location'
                ], 404);
            }

            $scanResult = ScanResult::where('device_id', $device->id)
                ->where('scan_id', $scanId)
                ->first();

            if (!$scanResult) {
                return response()->json([
                    'message' => 'Scan not found'
                ], 404);
            }

            return response()->json([
                'data' => [
                    'scan_id' => $scanResult->scan_id,
                    'status' => $scanResult->status,
                    'progress' => $this->getProgressPercentage($scanResult->status),
                    'scan_results_2g' => $scanResult->scan_results_2g,
                    'scan_results_5g' => $scanResult->scan_results_5g,
                    'optimal_channel_2g' => $scanResult->optimal_channel_2g,
                    'optimal_channel_5g' => $scanResult->optimal_channel_5g,
                    'nearby_networks_2g' => $scanResult->nearby_networks_2g,
                    'nearby_networks_5g' => $scanResult->nearby_networks_5g,
                    'interference_level_2g' => $scanResult->interference_level_2g,
                    'interference_level_5g' => $scanResult->interference_level_5g,
                    'error_message' => $scanResult->error_message,
                    'started_at' => $scanResult->started_at,
                    'completed_at' => $scanResult->completed_at,
                    'is_completed' => $scanResult->isCompleted(),
                    'is_failed' => $scanResult->isFailed(),
                    'is_in_progress' => $scanResult->isInProgress(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to get scan status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update scan status to started (called by device)
     */
    public function updateScanStarted(Request $request, $device_key, $device_secret, $scan_id)
    {
        try {
            $device = Device::where('device_key', $device_key)
                ->where('device_secret', $device_secret)
                ->first();

            if (!$device) {
                return response()->json(['error' => 'Invalid device credentials'], 401);
            }

            $scanResult = ScanResult::where('device_id', $device->id)
                ->where('scan_id', $scan_id)
                ->first();

            if (!$scanResult) {
                return response()->json(['error' => 'Scan not found'], 404);
            }

            $scanResult->markAsStarted();

            return response()->json([
                'message' => 'Scan status updated to started',
                'status' => $scanResult->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update scan status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update 2.4G scan results (called by device)
     */
    public function update2GScanResults(Request $request, $device_key, $device_secret, $scan_id)
    {
        Log::info('update2GScanResults called with device_key: ' . $device_key . ', device_secret: ' . $device_secret . ', scan_id: ' . $scan_id);
        Log::info('update2GScanResults called with request: ' );
        Log::info($request->all());
        try {
            $device = Device::where('device_key', $device_key)
                ->where('device_secret', $device_secret)
                ->first();

            if (!$device) {
                return response()->json(['error' => 'Invalid device credentials'], 401);
            }

            $scanResult = ScanResult::where('device_id', $device->id)
                ->where('scan_id', $scan_id)
                ->first();

            if (!$scanResult) {
                return response()->json(['error' => 'Scan not found'], 404);
            }

            $request->validate([
                'scan_results' => 'required|array',
                'scan_results.*.channel' => 'required|integer|min:1|max:14',
                'scan_results.*.signal' => 'required|integer|max:0',
                'scan_results.*.bssid' => 'required|string|regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/',
                'scan_results.*.ssid' => 'present|string',
                'nearby_networks' => 'required|integer|min:0',
                'interference_level' => 'required|in:low,medium,high'
            ]);

            $scanResult->update2GScanResults($request->all());

            return response()->json([
                'message' => '2.4G scan results updated successfully',
                'status' => $scanResult->status,
                'scan_results_2g' => $scanResult->scan_results_2g,
                'optimal_channel_2g' => $scanResult->optimal_channel_2g,
                'nearby_networks_2g' => $scanResult->nearby_networks_2g,
                'interference_level_2g' => $scanResult->interference_level_2g,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update 2.4G scan results: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update 5G scan results and complete scan (called by device)
     */
    public function update5GScanResults(Request $request, $device_key, $device_secret, $scan_id)
    {
        try {
            $device = Device::where('device_key', $device_key)
                ->where('device_secret', $device_secret)
                ->first();

            if (!$device) {
                return response()->json(['error' => 'Invalid device credentials'], 401);
            }

            $scanResult = ScanResult::where('device_id', $device->id)
                ->where('scan_id', $scan_id)
                ->first();

            if (!$scanResult) {
                return response()->json(['error' => 'Scan not found'], 404);
            }

            $request->validate([
                'scan_results' => 'required|array',
                'scan_results.*.channel' => 'required|integer|in:36,40,44,48,52,56,60,64,100,104,108,112,116,120,124,128,132,136,140,144,149,153,157,161,165',
                'scan_results.*.signal' => 'required|integer|max:0',
                'scan_results.*.bssid' => 'required|string|regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/',
                'scan_results.*.ssid' => 'present|string',
                'nearby_networks' => 'required|integer|min:0',
                'interference_level' => 'required|in:low,medium,high'
            ]);

            $scanResult->update5GScanResults($request->all());

            return response()->json([
                'message' => '5G scan results updated successfully. Scan completed.',
                'status' => $scanResult->status,
                'scan_results_5g' => $scanResult->scan_results_5g,
                'optimal_channel_5g' => $scanResult->optimal_channel_5g,
                'nearby_networks_5g' => $scanResult->nearby_networks_5g,
                'interference_level_5g' => $scanResult->interference_level_5g,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update 5G scan results: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark scan as failed (called by device)
     */
    public function markScanFailed(Request $request, $device_key, $device_secret, $scan_id)
    {
        try {
            $device = Device::where('device_key', $device_key)
                ->where('device_secret', $device_secret)
                ->first();

            if (!$device) {
                return response()->json(['error' => 'Invalid device credentials'], 401);
            }

            $scanResult = ScanResult::where('device_id', $device->id)
                ->where('scan_id', $scan_id)
                ->first();

            if (!$scanResult) {
                return response()->json(['error' => 'Scan not found'], 404);
            }

            $errorMessage = $request->input('error_message', 'Scan failed');
            $scanResult->markAsFailed($errorMessage);

            return response()->json([
                'message' => 'Scan marked as failed',
                'status' => $scanResult->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to mark scan as failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getLatestScanResults($locationId)
    {
        try {
            $location = Location::findOrFail($locationId);
            $device = $location->device;
            
            if (!$device) {
                return response()->json([
                    'message' => 'No device found for this location'
                ], 404);
            }
            
            // Get the latest completed scan result
            $scanResult = ScanResult::where('device_id', $device->id)
                ->where('status', ScanResult::STATUS_COMPLETED)
                ->orderBy('completed_at', 'desc')
                ->first();
                
            if (!$scanResult) {
                return response()->json(['error' => 'No scan results found'], 404);
            }

            return response()->json([
                'message' => 'Latest scan results retrieved successfully',
                'data' => [
                    'scan_id' => $scanResult->scan_id,
                    'status' => $scanResult->status,
                    'scan_results_2g' => $scanResult->scan_results_2g,
                    'scan_results_5g' => $scanResult->scan_results_5g,
                    'optimal_channel_2g' => $scanResult->optimal_channel_2g,
                    'optimal_channel_5g' => $scanResult->optimal_channel_5g,
                    'nearby_networks_2g' => $scanResult->nearby_networks_2g,
                    'nearby_networks_5g' => $scanResult->nearby_networks_5g,
                    'interference_level_2g' => $scanResult->interference_level_2g,
                    'interference_level_5g' => $scanResult->interference_level_5g,
                    'started_at' => $scanResult->started_at,
                    'completed_at' => $scanResult->completed_at,
                    'is_completed' => $scanResult->isCompleted(),
                    'is_failed' => $scanResult->isFailed(),
                    'is_in_progress' => $scanResult->isInProgress(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to get latest scan results: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get scan history for a location
     */
    public function getScanHistory($locationId)
    {
        try {
            $location = Location::findOrFail($locationId);
            $device = $location->device;
            
            if (!$device) {
                return response()->json([
                    'message' => 'No device found for this location'
                ], 404);
            }
            
            // Get all scan results for this device, ordered by most recent first
            $scanResults = ScanResult::where('device_id', $device->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($scanResult) {
                    return [
                        'scan_id' => $scanResult->scan_id,
                        'status' => $scanResult->status,
                        'scan_results_2g' => $scanResult->scan_results_2g,
                        'scan_results_5g' => $scanResult->scan_results_5g,
                        'optimal_channel_2g' => $scanResult->optimal_channel_2g,
                        'optimal_channel_5g' => $scanResult->optimal_channel_5g,
                        'nearby_networks_2g' => $scanResult->nearby_networks_2g,
                        'nearby_networks_5g' => $scanResult->nearby_networks_5g,
                        'interference_level_2g' => $scanResult->interference_level_2g,
                        'interference_level_5g' => $scanResult->interference_level_5g,
                        'error_message' => $scanResult->error_message,
                        'started_at' => $scanResult->started_at,
                        'completed_at' => $scanResult->completed_at,
                        'created_at' => $scanResult->created_at,
                        'is_completed' => $scanResult->isCompleted(),
                        'is_failed' => $scanResult->isFailed(),
                        'is_in_progress' => $scanResult->isInProgress(),
                    ];
                });

            return response()->json([
                'message' => 'Scan history retrieved successfully',
                'data' => $scanResults
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to get scan history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get progress percentage based on status
     */
    private function getProgressPercentage($status)
    {
        switch ($status) {
            case ScanResult::STATUS_INITIATED:
                return 0;
            case ScanResult::STATUS_STARTED:
                return 20;
            case ScanResult::STATUS_SCANNING_2G:
                return 50;
            case ScanResult::STATUS_SCANNING_5G:
                return 80;
            case ScanResult::STATUS_COMPLETED:
                return 100;
            case ScanResult::STATUS_FAILED:
                return 0;
            default:
                return 0;
        }
    }
}