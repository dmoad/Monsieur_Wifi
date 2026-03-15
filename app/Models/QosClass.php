<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QosClass extends Model
{
    protected $table = 'qos_classes';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'label',
        'dscp_value',
        'nft_mark',
        'priority',
        'description',
    ];

    protected $casts = [
        'dscp_value' => 'integer',
        'priority'   => 'integer',
    ];

    // Fixed class IDs — cannot be created or deleted via API
    const EF   = 'EF';
    const AF41 = 'AF41';
    const BE   = 'BE';
    const CS1  = 'CS1';

    public function domains(): HasMany
    {
        return $this->hasMany(QosDomain::class, 'class_id')->orderBy('domain');
    }
}
