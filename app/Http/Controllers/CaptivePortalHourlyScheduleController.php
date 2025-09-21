<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CaptivePortalHourlySchedule;
use App\Models\CaptivePortalWorkingHour;

class CaptivePortalHourlyScheduleController extends Controller
{
    /**
     * Get hourly schedule for a location
     */
    public function getHourlySchedule($locationId)
    {
        $schedule = CaptivePortalHourlySchedule::getWeeklySchedule($locationId);
        
        // If no hourly schedule exists, initialize from working hours
        if ($schedule->isEmpty()) {
            CaptivePortalHourlySchedule::initializeFromWorkingHours($locationId);
            $schedule = CaptivePortalHourlySchedule::getWeeklySchedule($locationId);
        }

        // Format the schedule for the frontend
        $formattedSchedule = [];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        
        foreach ($days as $day) {
            $daySchedule = $schedule->get($day, collect());
            $formattedSchedule[$day] = [];
            
            for ($hour = 0; $hour < 24; $hour++) {
                $hourData = $daySchedule->where('hour', $hour)->first();
                $formattedSchedule[$day][$hour] = [
                    'hour' => $hour,
                    'enabled' => $hourData ? $hourData->enabled : true,
                    'time_label' => sprintf('%02d:00', $hour)
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $formattedSchedule
        ], 200);
    }

    /**
     * Update hourly schedule for a location
     */
    public function updateHourlySchedule(Request $request, $locationId)
    {
        $validated = $request->validate([
            'schedule' => 'required|array',
            'schedule.*.day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'schedule.*.hour' => 'required|integer|min:0|max:23',
            'schedule.*.enabled' => 'required|boolean',
        ]);

        try {
            foreach ($validated['schedule'] as $scheduleEntry) {
                CaptivePortalHourlySchedule::updateOrCreate(
                    [
                        'location_id' => $locationId,
                        'day_of_week' => $scheduleEntry['day_of_week'],
                        'hour' => $scheduleEntry['hour'],
                    ],
                    [
                        'enabled' => $scheduleEntry['enabled'],
                    ]
                );
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Hourly schedule updated successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update hourly schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update schedule for a specific day
     */
    public function updateDaySchedule(Request $request, $locationId, $dayOfWeek)
    {
        $validated = $request->validate([
            'hours' => 'required|array',
            'hours.*.hour' => 'required|integer|min:0|max:23',
            'hours.*.enabled' => 'required|boolean',
        ]);

        if (!in_array($dayOfWeek, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid day of week'
            ], 400);
        }

        try {
            foreach ($validated['hours'] as $hourEntry) {
                CaptivePortalHourlySchedule::updateOrCreate(
                    [
                        'location_id' => $locationId,
                        'day_of_week' => $dayOfWeek,
                        'hour' => $hourEntry['hour'],
                    ],
                    [
                        'enabled' => $hourEntry['enabled'],
                    ]
                );
            }

            return response()->json([
                'status' => 'success',
                'message' => "Schedule for {$dayOfWeek} updated successfully"
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update day schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle a specific hour for a day
     */
    public function toggleHour(Request $request, $locationId, $dayOfWeek, $hour)
    {
        if (!in_array($dayOfWeek, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid day of week'
            ], 400);
        }

        if ($hour < 0 || $hour > 23) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid hour'
            ], 400);
        }

        try {
            $schedule = CaptivePortalHourlySchedule::where('location_id', $locationId)
                ->where('day_of_week', $dayOfWeek)
                ->where('hour', $hour)
                ->first();

            if ($schedule) {
                $schedule->enabled = !$schedule->enabled;
                $schedule->save();
            } else {
                CaptivePortalHourlySchedule::create([
                    'location_id' => $locationId,
                    'day_of_week' => $dayOfWeek,
                    'hour' => $hour,
                    'enabled' => false, // Default toggle to disabled
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Hour toggled successfully',
                'enabled' => $schedule ? $schedule->enabled : false
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to toggle hour: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Initialize hourly schedule from existing working hours
     */
    public function initializeFromWorkingHours($locationId)
    {
        try {
            CaptivePortalHourlySchedule::initializeFromWorkingHours($locationId);

            return response()->json([
                'status' => 'success',
                'message' => 'Hourly schedule initialized from working hours'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to initialize hourly schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check current status based on hourly schedule
     */
    public function checkCurrentStatus($locationId)
    {
        $currentDayOfWeek = strtolower(now()->format('l'));
        $currentHour = (int) now()->format('H');
        $currentTime = now()->format('H:i');
        $currentDateTime = now()->format('Y-m-d H:i:s');

        $isEnabled = CaptivePortalHourlySchedule::isEnabledNow($locationId);
        
        $schedule = CaptivePortalHourlySchedule::where('location_id', $locationId)
            ->where('day_of_week', $currentDayOfWeek)
            ->where('hour', $currentHour)
            ->first();

        $reason = $schedule 
            ? ($schedule->enabled ? "Enabled for hour {$currentHour}" : "Disabled for hour {$currentHour}")
            : "No hourly schedule configured, using legacy working hours";

        return response()->json([
            'status' => 'success',
            'data' => [
                'location_id' => $locationId,
                'current_day' => $currentDayOfWeek,
                'current_hour' => $currentHour,
                'current_time' => $currentTime,
                'current_datetime' => $currentDateTime,
                'captive_portal_enabled' => $isEnabled,
                'reason' => $reason,
                'hourly_schedule' => $schedule ? [
                    'day_of_week' => $schedule->day_of_week,
                    'hour' => $schedule->hour,
                    'enabled' => $schedule->enabled
                ] : null
            ]
        ], 200);
    }

    /**
     * Bulk update multiple hours for a day
     */
    public function bulkUpdateDay(Request $request, $locationId, $dayOfWeek)
    {
        $validated = $request->validate([
            'action' => 'required|in:enable_all,disable_all,enable_range,disable_range',
            'start_hour' => 'integer|min:0|max:23',
            'end_hour' => 'integer|min:0|max:23',
        ]);

        if (!in_array($dayOfWeek, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid day of week'
            ], 400);
        }

        try {
            $action = $validated['action'];
            
            if ($action === 'enable_all' || $action === 'disable_all') {
                $enabled = $action === 'enable_all';
                
                for ($hour = 0; $hour < 24; $hour++) {
                    CaptivePortalHourlySchedule::updateOrCreate(
                        [
                            'location_id' => $locationId,
                            'day_of_week' => $dayOfWeek,
                            'hour' => $hour,
                        ],
                        [
                            'enabled' => $enabled,
                        ]
                    );
                }
                
            } elseif ($action === 'enable_range' || $action === 'disable_range') {
                $startHour = $validated['start_hour'];
                $endHour = $validated['end_hour'];
                $enabled = $action === 'enable_range';
                
                if ($startHour > $endHour) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Start hour cannot be greater than end hour'
                    ], 400);
                }
                
                for ($hour = $startHour; $hour <= $endHour; $hour++) {
                    CaptivePortalHourlySchedule::updateOrCreate(
                        [
                            'location_id' => $locationId,
                            'day_of_week' => $dayOfWeek,
                            'hour' => $hour,
                        ],
                        [
                            'enabled' => $enabled,
                        ]
                    );
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => "Bulk update for {$dayOfWeek} completed successfully"
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to perform bulk update: ' . $e->getMessage()
            ], 500);
        }
    }
}
