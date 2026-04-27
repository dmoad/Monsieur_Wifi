<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class LocationNetworkQosDomain extends Model
{
    public $timestamps = false;

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected $table = 'location_network_qos_domains';

    protected $fillable = [
        'location_network_id',
        'class_id',
        'domain',
    ];

    public function network(): BelongsTo
    {
        return $this->belongsTo(LocationNetwork::class, 'location_network_id');
    }

    public function qosClass(): BelongsTo
    {
        return $this->belongsTo(QosClass::class, 'class_id');
    }

    /**
     * Seed this network's rows from the legacy global qos_class_domains table (for new networks).
     */
    public static function copyGlobalDefaultsToNetwork(LocationNetwork $network): void
    {
        $rows = DB::table('qos_class_domains')->where('class_id', '!=', QosClass::BE)->get();
        $now = now();
        foreach ($rows as $r) {
            DB::table('location_network_qos_domains')->insertOrIgnore([
                'location_network_id' => $network->id,
                'class_id' => $r->class_id,
                'domain' => $r->domain,
                'created_at' => $now,
            ]);
        }
    }
}
