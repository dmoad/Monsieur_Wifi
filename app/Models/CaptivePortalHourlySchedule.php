<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaptivePortalHourlySchedule extends Model
{
    protected $fillable = [
        'location_id',
        'day_of_week',
        'hour',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'hour' => 'integer',
    ];

    public $timestamps = true;

    /**
     * Get the location that owns the hourly schedule.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get hourly schedules for a specific location and day
     */
    public static function getScheduleForDay($locationId, $dayOfWeek)
    {
        return self::where('location_id', $locationId)
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('hour')
            ->get();
    }

    /**
     * Get complete weekly schedule for a location
     */
    public static function getWeeklySchedule($locationId)
    {
        return self::where('location_id', $locationId)
            ->orderByRaw("FIELD(day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")
            ->orderBy('hour')
            ->get()
            ->groupBy('day_of_week');
    }

    /**
     * Check if captive portal is enabled for current hour
     */
    public static function isEnabledNow($locationId)
    {
        $currentDayOfWeek = strtolower(now()->format('l'));
        $currentHour = (int) now()->format('H');

        $schedule = self::where('location_id', $locationId)
            ->where('day_of_week', $currentDayOfWeek)
            ->where('hour', $currentHour)
            ->first();

        // If no specific hourly schedule exists, check legacy working hours
        if (!$schedule) {
            $workingHour = CaptivePortalWorkingHour::where('location_id', $locationId)
                ->where('day_of_week', $currentDayOfWeek)
                ->first();

            if ($workingHour && $workingHour->start_time && $workingHour->end_time) {
                $currentTime = now()->format('H:i');
                $startTime = $workingHour->start_time;
                $endTime = $workingHour->end_time;

                // Handle overnight hours
                if ($endTime < $startTime) {
                    return $currentTime >= $startTime || $currentTime <= $endTime;
                } else {
                    return $currentTime >= $startTime && $currentTime <= $endTime;
                }
            }

            // Default to enabled if no schedule is found
            return true;
        }

        return $schedule->enabled;
    }

    /**
     * Initialize hourly schedule from existing working hours
     */
    public static function initializeFromWorkingHours($locationId)
    {
        $workingHours = CaptivePortalWorkingHour::where('location_id', $locationId)->get();
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($days as $day) {
            $workingHour = $workingHours->where('day_of_week', $day)->first();
            
            for ($hour = 0; $hour < 24; $hour++) {
                $enabled = true; // Default to enabled if no working hour record exists
                
                if ($workingHour) {
                    // If working hour exists but has null times, disable all hours
                    if (!$workingHour->start_time || !$workingHour->end_time) {
                        $enabled = false;
                    } else {
                        // Parse working hours and set enabled based on time range
                        $startHour = (int) substr($workingHour->start_time, 0, 2);
                        $endHour = (int) substr($workingHour->end_time, 0, 2);
                        
                        // Handle overnight hours
                        if ($endHour < $startHour) {
                            $enabled = $hour >= $startHour || $hour <= $endHour;
                        } else {
                            $enabled = $hour >= $startHour && $hour <= $endHour;
                        }
                    }
                }

                self::updateOrCreate(
                    [
                        'location_id' => $locationId,
                        'day_of_week' => $day,
                        'hour' => $hour,
                    ],
                    [
                        'enabled' => $enabled,
                    ]
                );
            }
        }
    }
}
