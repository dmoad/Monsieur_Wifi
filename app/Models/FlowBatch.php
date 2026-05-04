<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlowBatch extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'batch_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'batch_id',
        'device_id',
        'received_at',
        'record_count',
        'error_count',
    ];

    protected $casts = [
        'device_id' => 'integer',
        'received_at' => 'datetime',
        'record_count' => 'integer',
        'error_count' => 'integer',
    ];
}
