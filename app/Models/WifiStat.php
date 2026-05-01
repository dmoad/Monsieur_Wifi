<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WifiStat extends Model
{
    protected $fillable = [
        'device_id',
        'ap_id',
        'ap_mac',
        'config_version',
        'firmware_version',
        'ap_ts',
        'ap_ts_flagged',
        'radios',
        'received_at',
    ];

    protected $casts = [
        'ap_ts'        => 'datetime',
        'ap_ts_flagged' => 'boolean',
        'radios'       => 'array',
        'received_at'  => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(WifiStatClient::class);
    }
}
