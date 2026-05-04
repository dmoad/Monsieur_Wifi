<?php

namespace Tests\Feature;

use App\Jobs\ProcessFlowBatch;
use App\Models\Device;
use App\Models\FlowBatch;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * FlowIngestTest
 *
 * Uses DatabaseTransactions so rows are rolled back after every test.
 * Requires the migration to have been run: php artisan migrate
 *
 * Run with: php artisan test --filter=FlowIngestTest
 */
class FlowIngestTest extends TestCase
{
    use DatabaseTransactions;

    protected $connectionsToTransact = ['mysql'];

    protected Device $device;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['flow_sessions', 'flow_batches'] as $table) {
            if (! Schema::hasTable($table)) {
                $this->markTestSkipped('Run `php artisan migrate` before running FlowIngestTest.');
            }
        }

        $uid = uniqid();
        $this->device = Device::create([
            'name' => 'Test AP',
            'serial_number' => 'TEST-'.$uid,
            'mac_address' => '00-11-22-33-'.substr($uid, -2, 2).'-FF',
            'device_key' => 'testkey'.$uid,
            'device_secret' => 'testsecret'.$uid,
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function ingestUrl(): string
    {
        return "/api/devices/{$this->device->device_key}/{$this->device->device_secret}/flow-ingest";
    }

    private function queryUrl(): string
    {
        return "/api/devices/{$this->device->device_key}/{$this->device->device_secret}/flows";
    }

    private function makeNdjson(array $records): string
    {
        return implode("\n", array_map('json_encode', $records))."\n";
    }

    private function batchId(string $suffix = ''): string
    {
        return str_pad(dechex(abs(crc32(microtime().$suffix))), 8, '0', STR_PAD_LEFT);
    }

    private function sampleRecord(array $overrides = []): array
    {
        return array_merge([
            'ap_id' => 'TESTAPID01',
            'mac' => 'AA-BB-CC-DD-EE-FF',
            'src_ip' => '192.168.1.100',
            'dst_ip' => '8.8.8.8',
            'slot' => 1,
            'first_ts' => time() - 60,
            'last_ts' => time(),
            'hits' => 5,
        ], $overrides);
    }

    /**
     * POST to the ingest endpoint with a raw string body.
     * The controller just stores whatever it receives — no gzip required at this layer.
     */
    private function postIngest(string $body, string $batchId, array $extraHeaders = [])
    {
        $headers = array_merge([
            'HTTP_CONTENT_TYPE' => 'application/x-ndjson',
            'HTTP_X_BATCH_ID' => $batchId,
        ], $extraHeaders);

        return $this->call('POST', $this->ingestUrl(), [], [], [], $headers, $body);
    }

    // ── Tests: ingest endpoint ────────────────────────────────────────────────

    public function test_successful_ingest_returns_queued_true(): void
    {
        Queue::fake();
        Storage::fake('local');

        $batchId = $this->batchId('success');
        $body = $this->makeNdjson([$this->sampleRecord()]);

        $response = $this->postIngest($body, $batchId);

        $response->assertStatus(200)
            ->assertJson(['ok' => true, 'queued' => true]);

        Queue::assertPushed(ProcessFlowBatch::class, function ($job) use ($batchId) {
            return $job->batchId === $batchId && $job->deviceId === $this->device->id;
        });

        Storage::disk('local')->assertExists("flow-ingest/{$this->device->id}/{$batchId}.bin");
    }

    public function test_duplicate_batch_id_returns_duplicate_true_without_dispatching(): void
    {
        Queue::fake();

        $batchId = $this->batchId('dup');

        FlowBatch::create([
            'batch_id' => $batchId,
            'device_id' => $this->device->id,
            'received_at' => now(),
            'record_count' => 1,
            'error_count' => 0,
        ]);

        $response = $this->postIngest($this->makeNdjson([$this->sampleRecord()]), $batchId);

        $response->assertStatus(200)
            ->assertJson(['ok' => true, 'duplicate' => true]);

        Queue::assertNothingPushed();
    }

    public function test_invalid_credentials_returns_401(): void
    {
        $response = $this->call('POST', '/api/devices/bad-key/bad-secret/flow-ingest', [], [], [], [
            'HTTP_X_BATCH_ID' => $this->batchId(),
        ], 'body');

        $response->assertStatus(401);
    }

    public function test_missing_batch_id_returns_400(): void
    {
        $response = $this->call('POST', $this->ingestUrl(), [], [], [], [
            'HTTP_CONTENT_TYPE' => 'application/x-ndjson',
        ], 'body');

        $response->assertStatus(400)
            ->assertJsonFragment(['error' => 'Missing or invalid X-Batch-Id']);
    }

    public function test_empty_body_returns_400(): void
    {
        $response = $this->postIngest('', $this->batchId('empty'));

        $response->assertStatus(400)
            ->assertJsonFragment(['error' => 'Empty body']);
    }

    public function test_any_non_empty_body_is_accepted_and_queued(): void
    {
        Queue::fake();
        Storage::fake('local');

        // Controller never validates content — it's the job's responsibility.
        $batchId = $this->batchId('garbage');
        $response = $this->postIngest('THIS-IS-NOT-NDJSON', $batchId);

        $response->assertStatus(200)
            ->assertJson(['ok' => true, 'queued' => true]);

        Queue::assertPushed(ProcessFlowBatch::class);
    }

    // ── Tests: ProcessFlowBatch job ───────────────────────────────────────────

    public function test_job_inserts_rows_and_creates_batch_record(): void
    {
        $batchId = $this->batchId('job');
        $records = [
            $this->sampleRecord(['mac' => 'AA-BB-CC-DD-EE-FF', 'hits' => 3]),
            $this->sampleRecord(['mac' => '11-22-33-44-55-66', 'dst_ip' => '1.1.1.1', 'hits' => 7]),
        ];
        $body = gzencode($this->makeNdjson($records));
        $path = "flow-ingest/{$this->device->id}/{$batchId}.bin";

        Storage::disk('local')->put($path, $body);

        try {
            (new ProcessFlowBatch($this->device->id, $batchId, $path))->handle();

            $this->assertDatabaseHas('flow_batches', [
                'batch_id' => $batchId,
                'device_id' => $this->device->id,
                'record_count' => 2,
                'error_count' => 0,
            ]);

            $this->assertSame(2, DB::table('flow_sessions')->where('device_id', $this->device->id)->count());

            Storage::disk('local')->assertMissing($path);
        } finally {
            // Safety net — job deletes it, but clean up if test fails
            Storage::disk('local')->delete($path);
            DB::table('flow_batches')->where('batch_id', $batchId)->delete();
            DB::table('flow_sessions')->where('device_id', $this->device->id)->delete();
        }
    }

    public function test_job_skips_line_when_mac_has_no_hex_or_wrong_digit_count(): void
    {
        $batchId = $this->batchId('mac-garbage');
        $noHexDigits = json_encode(array_merge($this->sampleRecord(), [
            // Valid JSON/UTF-8; strips to empty — not a MAC.
            'mac' => 'UU-VV-WW-XX-YY-ZZ',
        ]));
        $wrongLenHex = json_encode(array_merge($this->sampleRecord(), [
            'mac' => 'aa:bb:cc:dd:ee:ffa',
        ]));
        $good = json_encode($this->sampleRecord(['hits' => 99]));
        $body = gzencode("{$noHexDigits}\n{$wrongLenHex}\n{$good}\n");
        $path = "flow-ingest/{$this->device->id}/{$batchId}.bin";

        Storage::disk('local')->put($path, $body);

        try {
            (new ProcessFlowBatch($this->device->id, $batchId, $path))->handle();

            $this->assertDatabaseHas('flow_batches', [
                'batch_id' => $batchId,
                'record_count' => 1,
                'error_count' => 2,
            ]);
            $this->assertTrue(
                DB::table('flow_sessions')->where('device_id', $this->device->id)->where('hits', 99)->exists()
            );
        } finally {
            Storage::disk('local')->delete($path);
            DB::table('flow_batches')->where('batch_id', $batchId)->delete();
            DB::table('flow_sessions')->where('device_id', $this->device->id)->delete();
        }
    }

    public function test_job_skips_malformed_json_lines_and_records_error_count(): void
    {
        $batchId = $this->batchId('malformed');
        $good = json_encode($this->sampleRecord());
        $bad = 'NOT_JSON';
        $missing = json_encode(['mac' => 'AA-BB-CC-DD-EE-FF']); // missing required fields
        $body = gzencode("{$good}\n{$bad}\n{$missing}\n");
        $path = "flow-ingest/{$this->device->id}/{$batchId}.bin";

        Storage::disk('local')->put($path, $body);

        try {
            (new ProcessFlowBatch($this->device->id, $batchId, $path))->handle();

            $this->assertDatabaseHas('flow_batches', [
                'batch_id' => $batchId,
                'record_count' => 1,
                'error_count' => 2,
            ]);
        } finally {
            Storage::disk('local')->delete($path);
            DB::table('flow_batches')->where('batch_id', $batchId)->delete();
            DB::table('flow_sessions')->where('device_id', $this->device->id)->delete();
        }
    }

    // ── Tests: query endpoint ─────────────────────────────────────────────────

    public function test_flows_query_returns_sessions_for_mac(): void
    {
        $mac = 'AA-BB-CC-DD-EE-FF';
        $now = time();

        DB::table('flow_sessions')->insert([
            'device_id' => $this->device->id,
            'mac' => $mac,
            'src_ip' => '192.168.1.1',
            'dst_ip' => '8.8.8.8',
            'slot' => 1,
            'first_ts' => $now - 3600,
            'last_ts' => $now - 3000,
            'hits' => 10,
        ]);

        $response = $this->getJson($this->queryUrl()."?mac={$mac}");

        $response->assertStatus(200)
            ->assertJsonFragment(['mac' => $mac, 'dst_ip' => '8.8.8.8', 'hits' => 10])
            ->assertJsonFragment(['count' => 1]);
    }

    public function test_flows_query_matches_mac_regardless_of_stored_separator(): void
    {
        $now = time();

        DB::table('flow_sessions')->insert([
            'device_id' => $this->device->id,
            'mac'       => 'aa:bb:cc:dd:ee:ff',
            'src_ip'    => '10.0.0.1',
            'dst_ip'    => '10.0.0.2',
            'slot'      => 0,
            'first_ts'  => $now - 100,
            'last_ts'   => $now,
            'hits'      => 1,
        ]);

        $response = $this->getJson($this->queryUrl().'?mac=AA-BB-CC-DD-EE-FF');

        $response->assertStatus(200)
            ->assertJsonFragment(['mac' => 'aa:bb:cc:dd:ee:ff', 'src_ip' => '10.0.0.1']);
    }

    public function test_flows_query_requires_valid_mac(): void
    {
        $response = $this->getJson($this->queryUrl().'?mac=not-a-mac');
        $response->assertStatus(400);
    }

    public function test_flows_query_respects_time_range(): void
    {
        $mac = 'AA-BB-CC-DD-EE-FF';
        $now = time();

        DB::table('flow_sessions')->insert([
            ['device_id' => $this->device->id, 'mac' => $mac, 'src_ip' => '0.0.0.0', 'dst_ip' => '0.0.0.0', 'slot' => 0, 'first_ts' => $now - 7200, 'last_ts' => $now - 7100, 'hits' => 1],
            ['device_id' => $this->device->id, 'mac' => $mac, 'src_ip' => '0.0.0.0', 'dst_ip' => '0.0.0.0', 'slot' => 0, 'first_ts' => $now - 1800, 'last_ts' => $now - 1700, 'hits' => 2],
        ]);

        $from = $now - 3600;
        $response = $this->getJson($this->queryUrl()."?mac={$mac}&from={$from}&to={$now}");

        $response->assertStatus(200);
        $this->assertSame(1, $response->json('count'));
        $this->assertSame(2, $response->json('sessions.0.hits'));
    }

    public function test_flows_query_returns_401_for_bad_credentials(): void
    {
        $response = $this->getJson('/api/devices/wrong/creds/flows?mac=AA-BB-CC-DD-EE-FF');
        $response->assertStatus(401);
    }
}
