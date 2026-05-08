<?php

namespace App\Console\Commands;

use App\Mail\LocationOfflineNotification;
use App\Models\Location;
use App\Models\LocationSettingsV2;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckOfflineHeartbeats extends Command
{
    protected $signature = 'locations:check-offline-heartbeats';

    protected $description = 'Send email when a location device has not heartbeated for 15 minutes';

    private const OFFLINE_MINUTES = 15;

    public function handle(): int
    {
        $threshold = Carbon::now()->subMinutes(self::OFFLINE_MINUTES);
        $sent = 0;

        Location::query()
            ->whereNotNull('device_id')
            ->with(['device', 'settings'])
            ->orderBy('id')
            ->chunkById(100, function ($locations) use ($threshold, &$sent) {
                foreach ($locations as $location) {
                    try {
                        if (! $location->device) {
                            continue;
                        }

                        $email = $location->settings?->offline_notification_email;
                        if ($email === null || trim((string) $email) === '') {
                            continue;
                        }

                        $rawLastSeen = $location->device->last_seen;
                        $lastSeen = $rawLastSeen ? Carbon::parse($rawLastSeen) : null;

                        $isOffline = $lastSeen === null || $lastSeen->lt($threshold);

                        if (! $isOffline) {
                            continue;
                        }

                        $settings = $location->settings
                            ?? LocationSettingsV2::firstOrCreate(
                                ['location_id' => $location->id],
                                [
                                    'web_filter_enabled' => false,
                                    'web_filter_categories' => [],
                                    'qos_enabled' => false,
                                ]
                            );

                        if ($settings->offline_notification_sent_at !== null) {
                            continue;
                        }

                        $locale = config('app.locale', 'en');

                        try {
                            Mail::to($email)
                                ->send(new LocationOfflineNotification($location, $location->device, $lastSeen, $locale));
                        } catch (\Throwable $e) {
                            Log::error('Offline heartbeat email failed for location '.$location->id.': '.$e->getMessage());

                            continue;
                        }

                        $settings->offline_notification_sent_at = Carbon::now();
                        $settings->save();
                        $sent++;
                    } catch (\Throwable $e) {
                        Log::error('CheckOfflineHeartbeats error for location '.$location->id.': '.$e->getMessage());
                    }
                }
            });

        $this->info("Offline heartbeat check complete. {$sent} notification(s) sent.");

        return Command::SUCCESS;
    }
}
