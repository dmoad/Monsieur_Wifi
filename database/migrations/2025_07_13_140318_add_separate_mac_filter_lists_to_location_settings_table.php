<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('location_settings', function (Blueprint $table) {
            $table->json('captive_mac_filter_list')->nullable()->after('mac_filter_list');
            $table->json('secured_mac_filter_list')->nullable()->after('captive_mac_filter_list');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('location_settings', function (Blueprint $table) {
            $table->dropColumn(['captive_mac_filter_list', 'secured_mac_filter_list']);
        });
    }
};
