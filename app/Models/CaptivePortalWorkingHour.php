<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaptivePortalWorkingHour extends Model
{
    protected $fillable = [
        'location_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    public $timestamps = true;
}