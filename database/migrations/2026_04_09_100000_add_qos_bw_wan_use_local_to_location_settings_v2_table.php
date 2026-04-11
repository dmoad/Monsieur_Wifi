<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('location_settings_v2', function (Blueprint $table) {
            $table->boolean('qos_bw_wan_use_local')->default(false)->after('qos_bw');
        });
    }

    public function down(): void
    {
        Schema::table('location_settings_v2', function (Blueprint $table) {
            $table->dropColumn('qos_bw_wan_use_local');
        });
    }
};
