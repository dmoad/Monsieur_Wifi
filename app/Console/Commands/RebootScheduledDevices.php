<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RebootScheduledDevices extends Command
{
    protected $signature = 'devices:reboot-scheduled';

    protected $description = 'Increment reboot_count for devices whose scheduled one-time reboot datetime matches the current server time';

    public function handle(): int
    {
        $now = Carbon::now();

        $locations = Location::whereNotNull('scheduled_reboot_time')
            ->whereNotNull('device_id')
            ->get();

        $triggered = 0;

        foreach ($locations as $location) {
            try {
                $scheduled = Carbon::parse($location->scheduled_reboot_time);

                // Fire if the scheduled time is in the past or within the current minute.
                // A 10-minute grace window catches reboots missed due to cron downtime.
                $isPastDue = $scheduled->lte($now) && $now->diffInMinutes($scheduled) <= 10;

                if ($isPastDue) {
                    $device = Device::find($location->device_id);
                    if ($device) {
                        $device->increment('reboot_count');
                        Log::info("Scheduled reboot triggered for location {$location->id} (device {$device->id}) at {$now->format('Y-m-d H:i')} (scheduled: {$scheduled->format('Y-m-d H:i')})");
                        $triggered++;
                    } else {
                        Log::warning("Scheduled reboot skipped for location {$location->id}: device {$location->device_id} not found");
                    }

                    $location->update(['scheduled_reboot_time' => null]);
                }
            } catch (\Exception $e) {
                Log::error("Error processing scheduled reboot for location {$location->id}: " . $e->getMessage());
            }
        }

        $this->info("Scheduled reboot check complete. {$triggered} device(s) rebooted.");

        return Command::SUCCESS;
    }
}
