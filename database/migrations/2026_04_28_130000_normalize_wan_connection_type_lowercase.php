<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Normalizes wan_connection_type values to lowercase so they match the
 * canonical form used by LocationSettingsV2::WAN_DHCP/WAN_STATIC/WAN_PPPOE
 * and the controller branches in LocationController. The frontend modal
 * was previously persisting uppercase values, which silently bypassed the
 * static/pppoe field-save branches.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['location_settings_v2', 'location_settings'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'wan_connection_type')) {
                DB::table($table)->update([
                    'wan_connection_type' => DB::raw('LOWER(wan_connection_type)'),
                ]);
            }
        }
    }

    public function down(): void
    {
        // No-op: lowercase is the canonical form; we don't restore the previous mixed casing.
    }
};
