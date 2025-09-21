<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all existing working hours and populate hourly schedules
        $workingHours = DB::table('captive_portal_working_hours')->get();
        $locations = DB::table('locations')->pluck('id');

        foreach ($locations as $locationId) {
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            
            foreach ($days as $day) {
                $workingHour = $workingHours->where('location_id', $locationId)
                    ->where('day_of_week', $day)
                    ->first();
                
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

                    DB::table('captive_portal_hourly_schedules')->insertOrIgnore([
                        'location_id' => $locationId,
                        'day_of_week' => $day,
                        'hour' => $hour,
                        'enabled' => $enabled,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove all hourly schedule entries
        DB::table('captive_portal_hourly_schedules')->truncate();
    }
};
