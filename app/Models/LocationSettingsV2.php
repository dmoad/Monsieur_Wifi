<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LocationSettingsV2 extends Model
{
    use HasFactory;

    protected $table = 'location_settings_v2';

    protected $fillable = [
        'location_id',

        // Radio / Channel
        'country_code',
        'transmit_power_2g',
        'transmit_power_5g',
        'channel_2g',
        'channel_5g',
        'channel_width_2g',
        'channel_width_5g',

        // WAN
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

        // VLAN
        'vlan_enabled',

        // Web filtering
        'web_filter_enabled',
        'web_filter_domains',
        'web_filter_categories',

        // QoS
        'qos_enabled',
        'qos_bw',
        'qos_bw_wan_use_local',
    ];

    protected $casts = [
        // Booleans
        'wan_enabled'        => 'boolean',
        'wan_nat_enabled'    => 'boolean',
        'vlan_enabled'       => 'boolean',
        'web_filter_enabled' => 'boolean',
        'qos_enabled'            => 'boolean',
        'qos_bw_wan_use_local'   => 'boolean',

        // Integers
        'transmit_power_2g'  => 'integer',
        'transmit_power_5g'  => 'integer',
        'channel_2g'         => 'integer',
        'channel_5g'         => 'integer',
        'channel_width_2g'   => 'integer',
        'channel_width_5g'   => 'integer',
        'wan_mtu'            => 'integer',

        // JSON
        'web_filter_domains'    => 'array',
        'web_filter_categories' => 'array',
        'qos_bw'                => 'array',
    ];

    // ── WAN connection type constants ────────────────────────────────────────

    const WAN_DHCP   = 'dhcp';
    const WAN_STATIC = 'static';
    const WAN_PPPOE  = 'pppoe';

    // ── Relationships ────────────────────────────────────────────────────────

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function networks(): HasMany
    {
        return $this->hasMany(LocationNetwork::class, 'location_id', 'location_id');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Whether the WAN connection requires static IP fields.
     */
    public function isStaticWan(): bool
    {
        return $this->wan_connection_type === self::WAN_STATIC;
    }

    /**
     * Whether the WAN connection uses PPPoE credentials.
     */
    public function isPppoeWan(): bool
    {
        return $this->wan_connection_type === self::WAN_PPPOE;
    }

    /**
     * Whether web filtering is active and has something to filter.
     */
    public function isWebFilteringActive(): bool
    {
        return $this->web_filter_enabled &&
               (!empty($this->web_filter_categories) || !empty($this->web_filter_domains));
    }

    /** @return array<string, int> */
    public static function defaultQosBw(): array
    {
        return [
            'wan_up_kbps'    => 0,
            'wan_down_kbps'  => 0,
            'voip_bw'        => 0,
            'streaming_bw'   => 0,
            'be_bw'          => 0,
            'bulk_bw'        => 0,
        ];
    }

    /**
     * Merge raw DB/array values into the canonical qos_bw shape (ints, defaults).
     *
     * @param  array<string, mixed>|null  $bw
     * @return array<string, int>
     */
    public static function normalizeQosBw(?array $bw): array
    {
        $defaults = self::defaultQosBw();
        if ($bw === null || $bw === []) {
            return $defaults;
        }
        $out = $defaults;
        foreach ($defaults as $key => $_) {
            if (! array_key_exists($key, $bw) || $bw[$key] === null || $bw[$key] === '') {
                continue;
            }
            $out[$key] = max(0, min(10_000_000, (int) $bw[$key]));
        }

        return $out;
    }
}
