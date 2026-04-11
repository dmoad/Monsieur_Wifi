<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('location_settings_v2', function (Blueprint $table) {
            $table->json('qos_bw')->nullable()->after('qos_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('location_settings_v2', function (Blueprint $table) {
            $table->dropColumn('qos_bw');
        });
    }
};
