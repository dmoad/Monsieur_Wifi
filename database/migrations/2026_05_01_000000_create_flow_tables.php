<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── flow_batches — idempotency + per-AP ingest visibility ─────────────
        DB::statement('
            CREATE TABLE flow_batches (
                batch_id      VARCHAR(32)     NOT NULL,
                device_id     BIGINT UNSIGNED NOT NULL,
                received_at   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
                record_count  INT UNSIGNED    NOT NULL,
                error_count   INT UNSIGNED    NOT NULL DEFAULT 0,
                PRIMARY KEY (batch_id),
                INDEX idx_device_received (device_id, received_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        // ── flow_sessions — the actual flow data, monthly-partitioned ─────────
        //
        // mac/src_ip/dst_ip are stored as ASCII strings exactly as emitted by the AP
        // (so DB browsing stays human-readable).
        //
        //   first_ts INT UNSIGNED — unix timestamp; partition key must appear in PRIMARY KEY
        //   ROW_FORMAT=COMPRESSED — 3–4x compression on repeated device_id/mac values
        //
        // The composite PK (id, first_ts) is required because MySQL demands the
        // partition key to appear in every unique index.
        DB::statement("
            CREATE TABLE flow_sessions (
                id          BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
                device_id   BIGINT UNSIGNED  NOT NULL,
                mac         VARCHAR(32)      NOT NULL,
                src_ip      VARCHAR(45)      NOT NULL,
                dst_ip      VARCHAR(45)      NOT NULL,
                slot        TINYINT UNSIGNED NOT NULL DEFAULT 0,
                first_ts    INT UNSIGNED     NOT NULL,
                last_ts     INT UNSIGNED     NOT NULL,
                hits        INT UNSIGNED     NOT NULL DEFAULT 1,

                PRIMARY KEY (id, first_ts),
                INDEX idx_device_mac_time (device_id, mac, first_ts),
                INDEX idx_device_time     (device_id, first_ts)
            ) ENGINE=InnoDB
              ROW_FORMAT=COMPRESSED
              DEFAULT CHARSET=utf8mb4
              PARTITION BY RANGE (first_ts) (
                PARTITION p202605 VALUES LESS THAN (UNIX_TIMESTAMP('2026-06-01')),
                PARTITION p202606 VALUES LESS THAN (UNIX_TIMESTAMP('2026-07-01')),
                PARTITION p202607 VALUES LESS THAN (UNIX_TIMESTAMP('2026-08-01')),
                PARTITION pfuture VALUES LESS THAN MAXVALUE
            )
        ");
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS flow_sessions');
        DB::statement('DROP TABLE IF EXISTS flow_batches');
    }
};
