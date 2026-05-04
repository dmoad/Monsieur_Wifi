<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model for flow_sessions.
 *
 * mac, src_ip, and dst_ip are stored as readable strings matching the ingest payload.
 */
class FlowSession extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    // ── first_ts / last_ts: INT UNSIGNED ↔ Carbon ────────────────────────────

    public function getFirstAtAttribute(): Carbon
    {
        return Carbon::createFromTimestamp($this->first_ts);
    }

    public function getLastAtAttribute(): Carbon
    {
        return Carbon::createFromTimestamp($this->last_ts);
    }
}
