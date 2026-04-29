<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDeviceLoginSession extends Model
{
    protected $table = 'user_device_login_sessions';

    protected $fillable = [
        'guest_network_user_id',
        'mac_address',
        'location_id',
        'network_id',
        'zone_id',
        'download_data',
        'upload_data',
        'login_type',
        'radius_session_id',
        'connect_time',
        'disconnect_time',
        'total_download',
        'total_upload',
        'session_duration',
        'login_success',
    ];

    protected $casts = [
        'zone_id' => 'integer',
        'network_id' => 'integer',
        'connect_time' => 'datetime',
        'disconnect_time' => 'datetime',
        'login_success' => 'boolean',
    ];

    public function guestNetworkUser(): BelongsTo
    {
        return $this->belongsTo(GuestNetworkUser::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function network(): BelongsTo
    {
        return $this->belongsTo(LocationNetwork::class);
    }
}
