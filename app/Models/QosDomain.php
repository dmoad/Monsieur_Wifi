<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QosDomain extends Model
{
    protected $table = 'qos_class_domains';

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'class_id',
        'domain',
    ];

    public function qosClass(): BelongsTo
    {
        return $this->belongsTo(QosClass::class, 'class_id');
    }
}
