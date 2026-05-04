<?php

namespace App\Jobs;

use App\Models\FlowBatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessFlowBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;

    public int $tries = 3;

    public array $backoff = [10, 30, 90];

    public function __construct(
        public readonly int $deviceId,
        public readonly string $batchId,
        public readonly string $payloadPath,
    ) {
        $this->onQueue(config('flows.queue', 'flow-ingest'));
    }

    public function handle(): void
    {
        $raw = Storage::disk('local')->get($this->payloadPath);

        if ($raw === null) {
            Log::warning("ProcessFlowBatch: payload file missing batch={$this->batchId}");

            return;
        }

        // ── Decompress ────────────────────────────────────────────────────────
        // Detect gzip magic bytes (\x1f\x8b) rather than trusting the header,
        // so the job is robust even if the web server strips Content-Encoding.
        $body = (substr($raw, 0, 2) === "\x1f\x8b") ? @gzdecode($raw) : $raw;

        if ($body === false) {
            Log::warning("ProcessFlowBatch: gzdecode failed batch={$this->batchId}");
            Storage::disk('local')->delete($this->payloadPath);

            return;
        }

        // ── Parse NDJSON ──────────────────────────────────────────────────────
        $rows = [];
        $errors = 0;

        foreach (preg_split('/\r?\n/', $body) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $r = json_decode($line, true);

            if (! $r || ! isset($r['mac'], $r['src_ip'], $r['dst_ip'], $r['first_ts'])) {
                $errors++;

                continue;
            }

            // Normalise MAC to plain hex (drops :, -, spaces, bogus UTF-8, etc.).
            // Persist only ascii `aa:bb:cc:dd:ee:ff` built from validated hex —
            // stray binary in the NDJSON can't slip into utf8mb4 `mac`.
            $macHex = strtolower(preg_replace('/[^a-fA-F0-9]/', '', (string) $r['mac']));
            if ($macHex === '' || ! preg_match('/^[0-9a-f]{12}\z/', $macHex)) {
                $errors++;

                continue;
            }
            $macStored = implode(':', str_split($macHex, 2));

            $srcIp = trim((string) $r['src_ip']);
            $dstIp = trim((string) $r['dst_ip']);
            if (filter_var($srcIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false
                || filter_var($dstIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
                $errors++;

                continue;
            }

            $rows[] = [
                'device_id' => $this->deviceId,
                'mac' => $macStored,
                'src_ip' => $srcIp,
                'dst_ip' => $dstIp,
                'slot' => (int) ($r['slot'] ?? 0),
                'first_ts' => (int) $r['first_ts'],
                'last_ts' => (int) ($r['last_ts'] ?? $r['first_ts']),
                'hits' => (int) ($r['hits'] ?? 1),
            ];
        }

        // ── Bulk insert + record batch atomically ─────────────────────────────
        // If the DB is slow or a chunk fails, the transaction rolls back and
        // the job retries (up to $tries times). The batch row acts as the
        // commit marker — idempotency is enforced by the controller before
        // dispatch, not here.
        DB::transaction(function () use ($rows, $errors) {
            $chunkSize = (int) config('flows.insert_chunk_size', 500);
            foreach (array_chunk($rows, $chunkSize) as $chunk) {
                DB::table('flow_sessions')->insert($chunk);
            }

            FlowBatch::create([
                'batch_id' => $this->batchId,
                'device_id' => $this->deviceId,
                'received_at' => now(),
                'record_count' => count($rows),
                'error_count' => $errors,
            ]);
        });

        Storage::disk('local')->delete($this->payloadPath);

        Log::info("ProcessFlowBatch: done batch={$this->batchId} device={$this->deviceId} records=".count($rows)." errors={$errors}");
    }

    public function failed(\Throwable $e): void
    {
        Log::error("ProcessFlowBatch: failed batch={$this->batchId} device={$this->deviceId}: {$e->getMessage()}");
        // Payload stays on disk deliberately — can be replayed manually once the
        // underlying issue is resolved.
    }
}
