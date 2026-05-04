<?php

namespace App\Console\Commands;

use App\Models\UserDeviceLoginSession;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CloseStaleGuestSessions extends Command
{
    protected $signature = 'sessions:close-stale
                            {--minutes=30 : Mark sessions stale after this many minutes of inactivity}
                            {--dry-run    : Report what would be closed without persisting changes}';

    protected $description = 'Close open guest sessions that have not received a RADIUS update in the configured number of minutes';

    public function handle(): int
    {
        $minutes = (int) $this->option('minutes');
        $dryRun  = (bool) $this->option('dry-run');
        $cutoff  = Carbon::now()->subMinutes($minutes);

        // A session is stale when it is still open (no disconnect_time) and either:
        //   a) last_update_time is set and older than the cutoff, or
        //   b) last_update_time is NULL and connect_time is older than the cutoff
        //      (sessions created before this column existed, or before first RADIUS packet).
        $query = UserDeviceLoginSession::whereNull('disconnect_time')
            ->where(function ($q) use ($cutoff) {
                $q->where(function ($inner) use ($cutoff) {
                    $inner->whereNotNull('last_update_time')
                          ->where('last_update_time', '<', $cutoff);
                })->orWhere(function ($inner) use ($cutoff) {
                    $inner->whereNull('last_update_time')
                          ->where('connect_time', '<', $cutoff);
                });
            });

        $sessions = $query->get();

        if ($sessions->isEmpty()) {
            $this->info('No stale sessions found.');
            return Command::SUCCESS;
        }

        $now   = Carbon::now();
        $count = 0;

        foreach ($sessions as $session) {
            $disconnectAt = $now;

            $duration = null;
            if ($session->connect_time !== null) {
                $duration = (int) $session->connect_time->diffInSeconds($disconnectAt);
            }

            if ($dryRun) {
                $this->line(sprintf(
                    '[dry-run] Would close session id=%d mac=%s last_update=%s connect=%s → duration=%ss',
                    $session->id,
                    $session->mac_address ?? 'n/a',
                    $session->last_update_time?->toIso8601String() ?? 'null',
                    $session->connect_time?->toIso8601String() ?? 'null',
                    $duration ?? '?',
                ));
            } else {
                $session->disconnect_time = $disconnectAt;

                if ($duration !== null && ($session->session_duration === null || (int) $session->session_duration === 0)) {
                    $session->session_duration = $duration;
                }

                $session->save();

                Log::info(sprintf(
                    'sessions:close-stale — closed session id=%d mac=%s (last_update=%s, cutoff=%s)',
                    $session->id,
                    $session->mac_address ?? 'n/a',
                    $session->last_update_time?->toIso8601String() ?? 'null',
                    $cutoff->toIso8601String(),
                ));
            }

            $count++;
        }

        $verb = $dryRun ? 'Would close' : 'Closed';
        $this->info("{$verb} {$count} stale session(s) (inactive > {$minutes} min).");

        return Command::SUCCESS;
    }
}
