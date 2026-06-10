<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('location_settings_v2', function (Blueprint $table) {
            $table->json('offline_notification_emails')->nullable()->after('qos_bw_wan_use_local');
        });

        // Migrate existing single-email rows into the new JSON array column.
        DB::table('location_settings_v2')
            ->whereNotNull('offline_notification_email')
            ->where('offline_notification_email', '!=', '')
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('location_settings_v2')
                        ->where('id', $row->id)
                        ->update([
                            'offline_notification_emails' => json_encode([$row->offline_notification_email]),
                        ]);
                }
            });

        Schema::table('location_settings_v2', function (Blueprint $table) {
            $table->dropColumn('offline_notification_email');
        });
    }

    public function down(): void
    {
        Schema::table('location_settings_v2', function (Blueprint $table) {
            $table->string('offline_notification_email')->nullable()->after('qos_bw_wan_use_local');
        });

        // Restore first email from the array back to the scalar column.
        DB::table('location_settings_v2')
            ->whereNotNull('offline_notification_emails')
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    $emails = json_decode($row->offline_notification_emails, true);
                    if (is_array($emails) && ! empty($emails)) {
                        DB::table('location_settings_v2')
                            ->where('id', $row->id)
                            ->update(['offline_notification_email' => $emails[0]]);
                    }
                }
            });

        Schema::table('location_settings_v2', function (Blueprint $table) {
            $table->dropColumn('offline_notification_emails');
        });
    }
};
