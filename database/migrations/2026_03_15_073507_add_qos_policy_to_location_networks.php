<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('location_networks', function (Blueprint $table) {
            // full = SNI inspect + honour client DSCP (trusted/home networks)
            // scavenger = blanket CS1 deprioritization (guest/IoT networks)
            $table->enum('qos_policy', ['full', 'scavenger'])->default('scavenger')->after('mac_filter_list');
        });
    }

    public function down(): void
    {
        Schema::table('location_networks', function (Blueprint $table) {
            $table->dropColumn('qos_policy');
        });
    }
};
