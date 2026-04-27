<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationQosDomain extends Model
{
    public $timestamps = false;

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected $table = 'location_qos_domains';

    protected $fillable = [
        'location_id',
        'class_id',
        'domain',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function qosClass(): BelongsTo
    {
        return $this->belongsTo(QosClass::class, 'class_id');
    }
}
