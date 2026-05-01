<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WifiStatClient extends Model
{
    // Bulk insert() is used at ingest time; ap_ts is the temporal anchor
    public $timestamps = false;

    protected $fillable = [
        'wifi_stat_id',
        'ap_ts',
        'location_id',
        'zone_id',
        'location_network_id',
        'guest_network_user_id',
        'slot',
        'ssid',
        'nasid',
        'network',
        'network_type',
        'radio',
        'iface',
        'band',
        'mac',
        'ip',
        'signal_dbm',
        'signal_avg_dbm',
        'snr_db',
        'tx_retries',
        'tx_failed',
        'connected_time_s',
        'inactive_time_ms',
    ];

    protected $casts = [
        'ap_ts' => 'datetime',
    ];

    public function wifiStat(): BelongsTo
    {
        return $this->belongsTo(WifiStat::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function locationNetwork(): BelongsTo
    {
        return $this->belongsTo(LocationNetwork::class);
    }

    public function guestNetworkUser(): BelongsTo
    {
        return $this->belongsTo(GuestNetworkUser::class);
    }

    /**
     * Persisted wifi_stat_clients.mac uses uppercase hyphenated hex (AA-BB-CC-DD-EE-FF).
     * Bulk INSERT bypasses Eloquent mutators; call this when assembling rows at ingest time.
     */
    public static function storageFormatMac(string $mac): string
    {
        $hex = preg_replace('/[^a-fA-F0-9]/', '', $mac);

        return strtoupper(implode('-', str_split($hex, 2)));
    }
}
