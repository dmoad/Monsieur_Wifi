<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Maintains monthly partitions on flow_sessions.
 *
 * Run daily. Logic:
 *   1. Add the partition for *next* month if it doesn't already exist, by
 *      reorganising `pfuture` (idempotent — the REORGANIZE just skips if the
 *      partition name already exists in the table definition).
 *   2. Drop partitions whose upper bound is older than the configured retention
 *      window. Uses ALTER TABLE DROP PARTITION, which removes the data segment
 *      in milliseconds — never DELETE on this table.
 *
 * Register in bootstrap/app.php:
 *   $schedule->command('flows:rotate-partitions')->dailyAt('02:00');
 */
class RotateFlowPartitions extends Command
{
    protected $signature = 'flows:rotate-partitions';

    protected $description = 'Add next month\'s partition and drop expired partitions on flow_sessions';

    public function handle(): int
    {
        $retentionMonths = (int) config('flows.retention_months', 1);
        $now = Carbon::now();

        // ── 1. Ensure next-month partition exists ─────────────────────────────
        $next = $now->copy()->addMonth()->startOfMonth();
        $partName = 'p'.$next->format('Ym');
        $boundary = $next->copy()->addMonth()->startOfMonth()->timestamp;

        try {
            DB::statement("
                ALTER TABLE flow_sessions REORGANIZE PARTITION pfuture INTO (
                    PARTITION {$partName} VALUES LESS THAN ({$boundary}),
                    PARTITION pfuture     VALUES LESS THAN MAXVALUE
                )
            ");
            $this->info("Created partition {$partName} (boundary {$boundary})");
        } catch (\Throwable $e) {
            // Most likely the partition already exists — safe to continue.
            $this->info("Skipped {$partName}: ".$e->getMessage());
        }

        // ── 2. Drop partitions older than retention window ─────────────────────
        $cutoffTs = $now->copy()->subMonths($retentionMonths)->startOfMonth()->timestamp;
        $partitions = DB::select("
            SELECT PARTITION_NAME, PARTITION_DESCRIPTION
            FROM information_schema.PARTITIONS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME   = 'flow_sessions'
              AND PARTITION_NAME != 'pfuture'
        ");

        foreach ($partitions as $p) {
            if ((int) $p->PARTITION_DESCRIPTION <= $cutoffTs) {
                DB::statement("ALTER TABLE flow_sessions DROP PARTITION {$p->PARTITION_NAME}");
                $this->info("Dropped {$p->PARTITION_NAME} (boundary {$p->PARTITION_DESCRIPTION})");
            }
        }

        $this->info('Partition rotation complete.');

        return Command::SUCCESS;
    }
}
