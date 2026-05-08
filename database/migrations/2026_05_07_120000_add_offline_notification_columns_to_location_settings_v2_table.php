<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('location_settings_v2', function (Blueprint $table) {
            $table->string('offline_notification_email')->nullable()->after('qos_bw_wan_use_local');
            $table->timestamp('offline_notification_sent_at')->nullable()->after('offline_notification_email');
        });
    }

    public function down(): void
    {
        Schema::table('location_settings_v2', function (Blueprint $table) {
            $table->dropColumn(['offline_notification_email', 'offline_notification_sent_at']);
        });
    }
};
