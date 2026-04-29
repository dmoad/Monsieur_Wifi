<?php

namespace App\Models;

use App\Support\IPv4Subnet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'auth_methods',
        'email_require_otp',
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
        'bridge_lan_dhcp_mode',
        'ip_address',
        'netmask',
        'gateway',
        'dns1',
        'dns2',
        'dhcp_enabled',
        'dhcp_start',
        'dhcp_end',
        'dhcp_lease_duration',
        'dhcp_reservations',

        // MAC filtering
        'mac_filter_mode',
        'mac_filter_list',

        // QoS
        'qos_policy',

        // Radio band
        'radio',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'visible' => 'boolean',
        'dhcp_enabled' => 'boolean',
        'dhcp_end' => 'integer',
        'dhcp_lease_duration' => 'integer',
        'mac_filter_list' => 'array',
        'dhcp_reservations' => 'array',
        'working_hours' => 'array',
        'auth_methods' => 'array',
        'email_require_otp' => 'boolean',
        'vlan_id' => 'integer',
        'session_timeout' => 'integer',
        'idle_timeout' => 'integer',
        'download_limit' => 'integer',
        'upload_limit' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $appends = ['dhcp_end_ip'];

    /**
     * Last IPv4 in the DHCP pool (derived from dhcp_start + pool size), for firmware/API consumers that expect an end address.
     */
    public function getDhcpEndIpAttribute(): ?string
    {
        if ($this->dhcp_start === null || $this->dhcp_end === null || (int) $this->dhcp_end < 1) {
            return null;
        }
        $start = IPv4Subnet::ipv4ToUint32($this->dhcp_start);
        if ($start === null) {
            return null;
        }
        $last = $start + (int) $this->dhcp_end - 1;
        if ($last > 0xFFFFFFFF) {
            return null;
        }
        $packed = $last > 0x7FFFFFFF ? $last - 0x100000000 : $last;

        return long2ip($packed);
    }

    /**
     * Valid network types.
     */
    const TYPES = ['password', 'captive_portal', 'open'];

    /**
     * QoS policy options.
     * full      — SNI inspect + honour client DSCP (trusted/home networks)
     * scavenger — blanket CS1 deprioritization (guest/IoT networks)
     */
    const QOS_POLICY_FULL = 'full';

    const QOS_POLICY_SCAVENGER = 'scavenger';

    const QOS_POLICIES = ['full', 'scavenger'];

    const RADIO_ALL = 'all';

    const RADIO_2GHZ = '2.4';

    const RADIO_5GHZ = '5';

    const RADIOS = ['all', '2.4', '5'];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function portalDesign(): BelongsTo
    {
        return $this->belongsTo(CaptivePortalDesign::class, 'portal_design_id');
    }

    /**
     * Per-network QoS SNI / hostname patterns per DSCP class (EF, AF41, CS1 — not BE).
     */
    public function qosDomains(): HasMany
    {
        return $this->hasMany(LocationNetworkQosDomain::class, 'location_network_id')->orderBy('domain');
    }
}
