<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Radcheck;

class LocationSettings extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'location_id',
        // Basic WiFi settings
        'wifi_name',
        'wifi_password',
        'wifi_visible',
        'wifi_security_type',
        
        // Captive Portal settings
        'captive_portal_enabled',
        'captive_portal_ssid',
        'captive_portal_visible',
        'captive_auth_method',
        'captive_portal_password',
        'redirect_url',
        'session_timeout',
        'idle_timeout',
        
        // Bandwidth and rate limiting
        'bandwidth_limit',
        'download_limit',
        'upload_limit',
        'rate_limiting_enabled',
        'max_devices_per_user',
        
        // Radio settings
        'country_code',
        'transmit_power_2g',
        'transmit_power_5g',
        'channel_2g',
        'channel_5g',
        'channel_width_2g',
        'channel_width_5g',
        
        // Access control
        'mac_filter_list',
        'captive_mac_filter_list',
        'secured_mac_filter_list',
        'web_filter_enabled',
        'web_filter_domains',
        'web_filter_categories',
        
        // Network settings
        'password_wifi_enabled',
        'password_wifi_ssid',
        'password_wifi_password',
        'password_wifi_security',
        'password_wifi_cipher_suites',
        'password_wifi_ip_mode',
        'password_wifi_ip',
        'password_wifi_netmask',
        'password_wifi_dhcp_enabled',
        'password_wifi_dhcp_start',
        'password_wifi_dhcp_end',
        
        // Captive portal IP settings
        'captive_portal_ip',
        'captive_portal_netmask',
        'captive_portal_gateway',
        'captive_portal_dhcp_enabled',
        'captive_portal_dhcp_start',
        'captive_portal_dhcp_end',
        
        // Quality of service
        'qos_enabled',
        'traffic_priority',
        'reserved_bandwidth',
        
        // User data and analytics
        'collect_user_data',
        'terms_enabled',
        'terms_content',
        'social_login_enabled',
        'enabled_social_platforms',
        
        // Theme and UI settings
        'theme_color',
        'logo_url',
        'welcome_message',
        'captive_portal_design',
        
        // System settings
        'analytics_enabled',
        
        // WAN Settings
        'wan_connection_type',
        'wan_ip_address',
        'wan_netmask',
        'wan_gateway',
        'wan_primary_dns',
        'wan_secondary_dns',
        'wan_pppoe_username',
        'wan_pppoe_password',
        'wan_pppoe_service_name',
        'wan_enabled',
        'wan_mac_address',
        'wan_mtu',
        'wan_nat_enabled',
        
        // VLAN Settings
        'password_wifi_vlan',
        'captive_portal_vlan',
        'captive_portal_redirect',
        'captive_portal_vlan_tagging',
        'password_wifi_vlan_tagging',
        'vlan_enabled',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        // Boolean casts
        'wifi_visible' => 'boolean',
        'captive_portal_enabled' => 'boolean',
        'captive_portal_visible' => 'boolean',
        'rate_limiting_enabled' => 'boolean',
        'web_filter_enabled' => 'boolean',
        'password_wifi_enabled' => 'boolean',
        'password_wifi_dhcp_enabled' => 'boolean',
        'captive_portal_dhcp_enabled' => 'boolean',
        'qos_enabled' => 'boolean',
        'collect_user_data' => 'boolean',
        'terms_enabled' => 'boolean',
        'social_login_enabled' => 'boolean',
        'analytics_enabled' => 'boolean',
        
        // JSON casts
        'enabled_social_platforms' => 'json',
        'mac_filter_list' => 'json',
        'captive_mac_filter_list' => 'json',
        'secured_mac_filter_list' => 'json',
        'web_filter_domains' => 'json',
        'web_filter_categories' => 'json',
        
        // Integer casts
        'session_timeout' => 'integer',
        'idle_timeout' => 'integer',
        'bandwidth_limit' => 'integer',
        'download_limit' => 'integer',
        'upload_limit' => 'integer',
        'max_devices_per_user' => 'integer',
        'transmit_power_2g' => 'integer',
        'transmit_power_5g' => 'integer',
        'channel_2g' => 'integer',
        'channel_5g' => 'integer',
        'channel_width_2g' => 'integer',
        'channel_width_5g' => 'integer',
        'reserved_bandwidth' => 'integer',
        
        // WAN Settings casts
        'wan_enabled' => 'boolean',
        'wan_nat_enabled' => 'boolean',
        'wan_mtu' => 'integer',
        
        // VLAN Settings casts
        'password_wifi_vlan' => 'integer',
        'captive_portal_vlan' => 'integer',
        'vlan_enabled' => 'boolean',
    ];

    /**
     * Get the location that owns the settings.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the device associated with the location settings.
     */
    public function device()
    {
        return $this->hasOneThrough(Device::class, Location::class, 'id', 'location_id', 'location_id');
    }

    /**
     * Check if the captive portal feature is active.
     *
     * @return bool
     */
    public function isCaptivePortalActive()
    {
        return $this->captive_portal_enabled && $this->location && $this->location->status === 'active';
    }

    /**
     * Get the full URL for the logo with fallback to default.
     *
     * @return string
     */
    public function getLogoUrlAttribute($value)
    {
        return !empty($value) ? $value : config('app.url') . '/images/default-logo.png';
    }

    /**
     * Check if data collection is allowed based on settings and privacy regulations.
     *
     * @return bool
     */
    public function isDataCollectionAllowed()
    {
        // Respect explicit settings first
        if (!$this->collect_user_data) {
            return false;
        }

        // Here you could implement additional checks based on the location country
        // and applicable privacy laws like GDPR for EU locations, etc.
        return true;
    }

    /**
     * Get the web filter categories as a collection.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWebFilterCategoriesCollection()
    {
        if (!$this->web_filter_categories || !is_array($this->web_filter_categories)) {
            return collect();
        }
        
        return Category::whereIn('id', $this->web_filter_categories)
                       ->enabled()
                       ->ordered()
                       ->get();
    }

    /**
     * Get the blocked domains based on selected categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBlockedDomainsFromCategories()
    {
        if (!$this->web_filter_enabled || !$this->web_filter_categories) {
            return collect();
        }
        
        return BlockedDomain::whereIn('category_id', $this->web_filter_categories)
                           ->active()
                           ->get();
    }

    /**
     * Check if web filtering is active and configured.
     *
     * @return bool
     */
    public function isWebFilteringActive()
    {
        return $this->web_filter_enabled && 
               ($this->web_filter_categories || $this->web_filter_domains);
    }

    /**
     * Determine if a guest device should be granted access based on current settings.
     *
     * @param array $deviceData Information about the device requesting access
     * @return bool
     */
    public function shouldAllowAccess($deviceData)
    {
        // Example implementation - this would be customized based on your business logic
        if (!$this->captive_portal_enabled) {
            return true; // Open network, always allow
        }
        
        if ($this->max_devices_per_user > 0) {
            // Here you would check the current device count for the user
            // For illustration only:
            // $currentDeviceCount = UserDevice::where('user_id', $deviceData['user_id'])->count();
            // if ($currentDeviceCount >= $this->max_devices_per_user) {
            //     return false;
            // }
        }
        
        return true;
    }
    
    /**
     * Check if MAC filtering is enabled (has any MAC addresses configured).
     *
     * @return bool
     */
    public function isMacFilteringEnabled()
    {
        return !empty($this->mac_filter_list);
    }
    
    /**
     * Get MAC filter statistics.
     *
     * @return array
     */
    public function getMacFilterStatsAttribute()
    {
        $macList = $this->mac_filter_list ?: [];
        $stats = [
            'total' => 0,
            'whitelisted' => 0,
            'blacklisted' => 0
        ];
        
        foreach ($macList as $macItem) {
            $stats['total']++;
            if (isset($macItem['type'])) {
                if ($macItem['type'] === 'whitelist') {
                    $stats['whitelisted']++;
                } elseif ($macItem['type'] === 'blacklist') {
                    $stats['blacklisted']++;
                }
            } else {
                // Old format compatibility - treat as blacklist
                $stats['blacklisted']++;
            }
        }
        
        return $stats;
    }
    
    /**
     * Check if a MAC address should be allowed access based on filter settings.
     *
     * @param string $macAddress
     * @return bool
     */
    public function shouldAllowMacAddress($macAddress)
    {
        // Normalize MAC address to dash-delimited uppercase format
        $macAddress = $this->normalizeMacAddress($macAddress);
        
        // If MAC address is invalid, deny access
        if ($macAddress === null) {
            return false;
        }
        
        // Get the MAC filter list
        $macList = $this->mac_filter_list ?: [];
        
        // If no MAC addresses configured, allow all
        if (empty($macList)) {
            return true;
        }
        
        $hasWhitelist = false;
        $hasBlacklist = false;
        $isWhitelisted = false;
        $isBlacklisted = false;
        
        // Check each MAC filter entry
        foreach ($macList as $macItem) {
            // Handle both old format (string) and new format (object)
            if (is_string($macItem)) {
                // Old format - treat as blacklist
                $mac = $macItem;
                $type = 'blacklist';
            } elseif (isset($macItem['mac']) && isset($macItem['type'])) {
                // New format
                $mac = $macItem['mac'];
                $type = $macItem['type'];
            } else {
                continue; // Skip invalid entries
            }
            
            if ($mac === $macAddress) {
                if ($type === 'whitelist') {
                    $isWhitelisted = true;
                } elseif ($type === 'blacklist') {
                    $isBlacklisted = true;
                }
            }
            
            // Track what types of filters exist
            if ($type === 'whitelist') {
                $hasWhitelist = true;
            } elseif ($type === 'blacklist') {
                $hasBlacklist = true;
            }
        }
        
        // Apply filtering logic
        if ($isBlacklisted) {
            // Explicitly blacklisted - always deny
            return false;
        }
        
        if ($hasWhitelist && !$isWhitelisted) {
            // Whitelist exists but MAC is not in it - deny
            return false;
        }
        
        // Allow in all other cases (explicitly whitelisted or no relevant filters)
        return true;
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
     * Add a MAC address to the filter list.
     *
     * @param string $macAddress
     * @param string $type 'whitelist' or 'blacklist'
     * @return bool
     */
    public function addMacAddress($macAddress, $type = 'blacklist')
    {
        // Normalize MAC address to dash-delimited uppercase format
        $macAddress = $this->normalizeMacAddress($macAddress);
        
        // Validate MAC address format (dash-delimited)
        if ($macAddress === null || !preg_match('/^([0-9A-F]{2}-){5}([0-9A-F]{2})$/', $macAddress)) {
            return false;
        }
        
        // Validate type
        if (!in_array($type, ['whitelist', 'blacklist'])) {
            return false;
        }
        
        // Get current list
        $macList = $this->mac_filter_list ?: [];
        
        // Check if MAC already exists
        foreach ($macList as $macItem) {
            $existingMac = is_string($macItem) ? $macItem : ($macItem['mac'] ?? '');
            if ($existingMac === $macAddress) {
                return false; // Already exists
            }
        }
        
        // Add new MAC address
        $macList[] = [
            'mac' => $macAddress,
            'type' => $type
        ];
        
        $this->mac_filter_list = $macList;
        
        // Create corresponding radcheck record
        $accessControl = $type === 'whitelist' ? 'whitelisted' : 'blacklisted';
        Radcheck::updateOrCreateRecord(
            $macAddress,
            'Cleartext-Password',
            $macAddress,
            '==',
            [
                'location_id' => $this->location_id,
                'access_control' => $accessControl
            ]
        );
        
        return true;
    }
    
    /**
     * Remove a MAC address from the filter list.
     *
     * @param string $macAddress
     * @return bool
     */
    public function removeMacAddress($macAddress)
    {
        // Normalize MAC address to dash-delimited uppercase format
        $macAddress = $this->normalizeMacAddress($macAddress);
        
        // Return false if MAC address is invalid
        if ($macAddress === null) {
            return false;
        }
        
        // Get current list
        $macList = $this->mac_filter_list ?: [];
        $newMacList = [];
        $found = false;
        
        foreach ($macList as $macItem) {
            $existingMac = is_string($macItem) ? $macItem : ($macItem['mac'] ?? '');
            if ($existingMac !== $macAddress) {
                $newMacList[] = $macItem;
            } else {
                $found = true;
            }
        }
        
        if ($found) {
            $this->mac_filter_list = $newMacList;
            
            // Remove corresponding radcheck record
            Radcheck::where('username', $macAddress)
                ->where('attribute', 'Cleartext-Password')
                ->where('location_id', $this->location_id)
                ->delete();
            
            return true;
        }
        
        return false; // Not found
    }
    
    /**
     * Get the count of MAC addresses in the filter list.
     *
     * @return int
     */
    public function getMacFilterCountAttribute()
    {
        return count($this->mac_filter_list ?: []);
    }
    
    /**
     * Get all whitelisted MAC addresses.
     *
     * @return array
     */
    public function getWhitelistedMacAddresses()
    {
        $macList = $this->mac_filter_list ?: [];
        $whitelisted = [];
        
        foreach ($macList as $macItem) {
            if (isset($macItem['type']) && $macItem['type'] === 'whitelist' && isset($macItem['mac'])) {
                $whitelisted[] = $macItem['mac'];
            }
        }
        
        return $whitelisted;
    }
    
    /**
     * Get all blacklisted MAC addresses.
     *
     * @return array
     */
    public function getBlacklistedMacAddresses()
    {
        $macList = $this->mac_filter_list ?: [];
        $blacklisted = [];
        
        foreach ($macList as $macItem) {
            if (is_string($macItem)) {
                // Old format - treat as blacklist
                $blacklisted[] = $macItem;
            } elseif (isset($macItem['type']) && $macItem['type'] === 'blacklist' && isset($macItem['mac'])) {
                $blacklisted[] = $macItem['mac'];
            }
        }
        
        return $blacklisted;
    }
}
