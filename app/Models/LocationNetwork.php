<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationNetwork extends Model
{
    protected $fillable = [
        'location_id',
        'sort_order',
        'type',
        'enabled',
        'ssid',
        'visible',
        'vlan_id',
        'vlan_tagging',

        // Password WiFi fields
        'password',
        'security',
        'cipher_suites',

        // Captive Portal fields
        'auth_method',
        'portal_password',
        'social_auth_method',
        'session_timeout',
        'idle_timeout',
        'redirect_url',
        'portal_design_id',
        'download_limit',
        'upload_limit',
        'working_hours',

        // Shared IP / DHCP fields
        'ip_mode',
        'ip_address',
        'netmask',
        'gateway',
        'dns1',
        'dns2',
        'dhcp_enabled',
        'dhcp_start',
        'dhcp_end',

        // MAC filtering
        'mac_filter_mode',
        'mac_filter_list',

        // QoS
        'qos_policy',
    ];

    protected $casts = [
        'enabled'       => 'boolean',
        'visible'       => 'boolean',
        'dhcp_enabled'  => 'boolean',
        'mac_filter_list' => 'array',
        'working_hours'   => 'array',
        'vlan_id'       => 'integer',
        'session_timeout' => 'integer',
        'idle_timeout'  => 'integer',
        'download_limit' => 'integer',
        'upload_limit'  => 'integer',
        'sort_order'    => 'integer',
    ];

    /**
     * Valid network types.
     */
    const TYPES = ['password', 'captive_portal', 'open'];

    /**
     * QoS policy options.
     * full      — SNI inspect + honour client DSCP (trusted/home networks)
     * scavenger — blanket CS1 deprioritization (guest/IoT networks)
     */
    const QOS_POLICY_FULL      = 'full';
    const QOS_POLICY_SCAVENGER = 'scavenger';
    const QOS_POLICIES         = ['full', 'scavenger'];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function portalDesign(): BelongsTo
    {
        return $this->belongsTo(CaptivePortalDesign::class, 'portal_design_id');
    }
}
