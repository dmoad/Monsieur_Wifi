<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessFlowBatch;
use App\Models\Device;
use App\Models\FlowBatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FlowIngestController extends Controller
{
    /**
     * POST /api/devices/{device_key}/{device_secret}/flow-ingest
     *
     * Receives gzip-compressed NDJSON flow records from an AP, persists the raw
     * payload to local disk, and dispatches a queued job to process it.
     * Returns in <50 ms so the AP never times out.
     */
    public function flowIngest(string $device_key, string $device_secret, Request $request): JsonResponse
    {
        $raw = $request->getContent();
        Log::info('FlowIngestController::flowIngest', [
            'device_key' => $device_key,
            'content_type' => $request->header('Content-Type'),
            'x_batch_id' => $request->header('X-Batch-Id'),
            'body_bytes' => strlen($raw),
        ]);

        // ── Authenticate ──────────────────────────────────────────────────────
        $device = Device::where('device_key', $device_key)
            ->where('device_secret', $device_secret)
            ->first();

        if (! $device) {
            return response()->json(['error' => 'Invalid device credentials'], 401);
        }

        // ── Validate X-Batch-Id ───────────────────────────────────────────────
        $batchId = $request->header('X-Batch-Id', '');

        if (! preg_match('/^[a-f0-9]{8,32}$/i', $batchId)) {
            return response()->json(['error' => 'Missing or invalid X-Batch-Id'], 400);
        }

        // ── Idempotency check (cheap PRIMARY KEY lookup) ──────────────────────
        if (FlowBatch::where('batch_id', $batchId)->exists()) {
            return response()->json(['ok' => true, 'duplicate' => true]);
        }

        if ($raw === '' || $raw === false) {
            return response()->json(['error' => 'Empty body'], 400);
        }

        // ── Dump to local disk → queue job ────────────────────────────────────
        // Local disk is transient; the job deletes it after a successful insert.
        // Keeping it on disk (not in the jobs table payload) avoids serialising
        // megabytes of binary into the queue backend.
        $path = "flow-ingest/{$device->id}/{$batchId}.bin";
        Storage::disk('local')->put($path, $raw);

        ProcessFlowBatch::dispatch($device->id, $batchId, $path);

        return response()->json(['ok' => true, 'queued' => true]);
    }

    /**
     * GET /api/devices/{device_key}/{device_secret}/flows
     *
     * Query params:
     *   mac    — required; "AA-BB-CC-DD-EE-FF" or "aa:bb:cc:dd:ee:ff"
     *   from   — unix timestamp, default: now - 24h
     *   to     — unix timestamp, default: now
     *   limit  — max rows returned, default 1000, max 10000
     */
    public function flowsQuery(string $device_key, string $device_secret, Request $request): JsonResponse
    {
        $device = Device::where('device_key', $device_key)
            ->where('device_secret', $device_secret)
            ->first();

        if (! $device) {
            return response()->json(['error' => 'Invalid device credentials'], 401);
        }

        $mac = (string) $request->query('mac', '');
        $from = (int) $request->query('from', time() - 86400);
        $to = (int) $request->query('to', time());
        $limit = min((int) $request->query('limit', 1000), 10000);

        if (! preg_match('/^[0-9A-Fa-f]{2}([-:][0-9A-Fa-f]{2}){5}$/', $mac)) {
            return response()->json(['error' => 'Valid mac required (AA-BB-CC-DD-EE-FF or aa:bb:cc:dd:ee:ff)'], 400);
        }

        $macHexNorm = strtolower(str_replace(['-', ':'], '', $mac));

        $sessions = DB::table('flow_sessions')
            ->where('device_id', $device->id)
            ->whereRaw('LOWER(REPLACE(REPLACE(mac, "-", ""), ":", "")) = ?', [$macHexNorm])
            ->whereBetween('first_ts', [$from, $to])
            ->orderBy('first_ts', 'desc')
            ->limit($limit)
            ->get();

        $out = $sessions->map(fn ($r) => [
            'mac' => $r->mac,
            'src_ip' => $r->src_ip,
            'dst_ip' => $r->dst_ip,
            'slot' => $r->slot,
            'first_ts' => $r->first_ts,
            'last_ts' => $r->last_ts,
            'hits' => $r->hits,
        ]);

        return response()->json(['sessions' => $out, 'count' => $out->count()]);
    }
}
