<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $column = DB::selectOne(
            'SHOW COLUMNS FROM flow_sessions WHERE Field = ?',
            ['mac']
        );

        if (! $column || ! str_contains(strtolower((string) $column->Type), 'binary')) {
            return;
        }

        DB::statement('ALTER TABLE flow_sessions DROP INDEX idx_device_mac_time');

        DB::statement('ALTER TABLE flow_sessions
            CHANGE COLUMN mac mac_bin BINARY(6) NOT NULL,
            CHANGE COLUMN src_ip src_u32 INT UNSIGNED NOT NULL,
            CHANGE COLUMN dst_ip dst_u32 INT UNSIGNED NOT NULL');

        DB::statement('ALTER TABLE flow_sessions
            ADD COLUMN mac VARCHAR(32) NOT NULL DEFAULT "" AFTER mac_bin,
            ADD COLUMN src_ip VARCHAR(45) NOT NULL DEFAULT "" AFTER src_u32,
            ADD COLUMN dst_ip VARCHAR(45) NOT NULL DEFAULT "" AFTER dst_u32');

        foreach (DB::table('flow_sessions')->cursor() as $row) {
            $ipv4Str = fn (mixed $u): string => long2ip((int) sprintf('%u', (int) $u));

            DB::table('flow_sessions')
                ->where('id', $row->id)
                ->where('first_ts', $row->first_ts)
                ->update([
                    'mac' => strtolower(implode(':', str_split(bin2hex($row->mac_bin), 2))),
                    'src_ip' => $ipv4Str($row->src_u32),
                    'dst_ip' => $ipv4Str($row->dst_u32),
                ]);
        }

        DB::statement('ALTER TABLE flow_sessions
            DROP COLUMN mac_bin,
            DROP COLUMN src_u32,
            DROP COLUMN dst_u32');

        DB::statement('ALTER TABLE flow_sessions ADD INDEX idx_device_mac_time (device_id, mac, first_ts)');
    }

    public function down(): void
    {
        //
    }
};
