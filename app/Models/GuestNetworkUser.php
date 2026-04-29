<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuestNetworkUser extends Model
{
    protected $fillable = [
        'name',
        'mac_address',
        'location_id',
        'network_id',
        'zone_id',
        'expiration_time',
        'download_bandwidth',
        'upload_bandwidth',
        'blocked',
        'email',
        'phone',
        'os',
        'device_type',
    ];

    protected $casts = [
        'expiration_time' => 'datetime',
        'blocked' => 'boolean',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function network(): BelongsTo
    {
        return $this->belongsTo(LocationNetwork::class);
    }

    public function loginSessions(): HasMany
    {
        return $this->hasMany(UserDeviceLoginSession::class);
    }
}
