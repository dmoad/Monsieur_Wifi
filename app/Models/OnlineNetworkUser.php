<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineNetworkUser extends Model
{
    protected $fillable = [
        'mac',
        'type',
        'ip',
        'interface',
        'hostname',
        'network',
        'location_id',
    ];

    /**
     * Get the location that owns the online network user.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
