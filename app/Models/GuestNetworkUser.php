<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    protected $casts = [
        'expiration_time' => 'datetime',
        'blocked'         => 'boolean',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function network(): BelongsTo
    {
        return $this->belongsTo(LocationNetwork::class);
    }
}
