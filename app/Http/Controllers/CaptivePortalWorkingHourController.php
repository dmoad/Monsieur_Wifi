<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CaptivePortalWorkingHour;

class CaptivePortalWorkingHourController extends Controller
{
    /**
     * Update or create working hours for a location.
     */
    public function updateWeeklySchedule(Request $request, $locationId)
    {
        $validated = $request->validate([
            '*.day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            '*.start_time' => 'nullable|date_format:H:i',
            '*.end_time' => 'nullable|date_format:H:i',
        ]);

        foreach ($validated as $dayEntry) {
            CaptivePortalWorkingHour::updateOrCreate(
                [
                    'location_id' => $locationId,
                    'day_of_week' => $dayEntry['day_of_week'],
                ],
                [
                    'start_time' => $dayEntry['start_time'],
                    'end_time' => $dayEntry['end_time'],
                ]
            );
        }

        return response()->json(['message' => 'Working hours updated'], 200);
    }

    /**
     * Get weekly working hours for a location.
     */
    public function getSchedule($locationId)
    {
        $data = CaptivePortalWorkingHour::where('location_id', $locationId)
            ->orderByRaw("FIELD(day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")
            ->get();

        return response()->json($data);
    }

    /**
     * Update working hours for a location (API route method).
     */
    public function updateWorkingHours(Request $request, $locationId)
    {
        $validated = $request->validate([
            'working_hours' => 'required|array',
            'working_hours.*.day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'working_hours.*.start_time' => 'nullable|date_format:H:i',
            'working_hours.*.end_time' => 'nullable|date_format:H:i',
            'working_hours.*.enabled' => 'boolean',
        ]);

        foreach ($validated['working_hours'] as $dayEntry) {
            CaptivePortalWorkingHour::updateOrCreate(
                [
                    'location_id' => $locationId,
                    'day_of_week' => $dayEntry['day_of_week'],
                ],
                [
                    'start_time' => $dayEntry['enabled'] ? $dayEntry['start_time'] : null,
                    'end_time' => $dayEntry['enabled'] ? $dayEntry['end_time'] : null,
                ]
            );
        }

        return response()->json([
            'message' => 'Working hours updated successfully',
            'status' => 'success'
        ], 200);
    }

    /**
     * Get working hours for a location (API route method).
     */
    public function getWorkingHours($locationId)
    {
        $workingHours = CaptivePortalWorkingHour::where('location_id', $locationId)
            ->orderByRaw("FIELD(day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")
            ->get();

        // If no working hours exist, return default structure
        if ($workingHours->isEmpty()) {
            $defaultDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            $workingHours = collect($defaultDays)->map(function ($day) {
                return [
                    'day_of_week' => $day,
                    'start_time' => null,
                    'end_time' => null,
                    'enabled' => false
                ];
            });
        } else {
            $workingHours = $workingHours->map(function ($item) {
                return [
                    'day_of_week' => $item->day_of_week,
                    'start_time' => $item->start_time,
                    'end_time' => $item->end_time,
                    'enabled' => !is_null($item->start_time) && !is_null($item->end_time)
                ];
            });
        }

        return response()->json([
            'status' => 'success',
            'data' => $workingHours
        ], 200);
    }

    /**
     * Check if captive portal is currently enabled based on working hours
     */
    public function checkCurrentStatus($locationId)
    {
        // Get current day of week and time
        $currentDayOfWeek = strtolower(now()->format('l'));
        $currentTime = now()->format('H:i');
        $currentDateTime = now()->format('Y-m-d H:i:s');
        
        // Get working hours for current day
        $workingHour = CaptivePortalWorkingHour::where('location_id', $locationId)
            ->where('day_of_week', $currentDayOfWeek)
            ->first();
        
        $isEnabled = true;
        $reason = '24/7 access (no working hours configured)';
        
        if ($workingHour && $workingHour->start_time && $workingHour->end_time) {
            $startTime = $workingHour->start_time;
            $endTime = $workingHour->end_time;
            
            // Handle overnight hours
            if ($endTime < $startTime) {
                $isEnabled = $currentTime >= $startTime || $currentTime <= $endTime;
                $reason = $isEnabled 
                    ? "Within overnight working hours ({$startTime} - {$endTime})"
                    : "Outside overnight working hours ({$startTime} - {$endTime})";
            } else {
                $isEnabled = $currentTime >= $startTime && $currentTime <= $endTime;
                $reason = $isEnabled 
                    ? "Within working hours ({$startTime} - {$endTime})"
                    : "Outside working hours ({$startTime} - {$endTime})";
            }
        }
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'location_id' => $locationId,
                'current_day' => $currentDayOfWeek,
                'current_time' => $currentTime,
                'current_datetime' => $currentDateTime,
                'captive_portal_enabled' => $isEnabled,
                'reason' => $reason,
                'working_hours' => $workingHour ? [
                    'start_time' => $workingHour->start_time,
                    'end_time' => $workingHour->end_time
                ] : null
            ]
        ], 200);
    }
}