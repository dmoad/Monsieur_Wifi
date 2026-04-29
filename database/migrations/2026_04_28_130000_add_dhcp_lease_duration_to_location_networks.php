<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('location_networks', function (Blueprint $table) {
            // Minutes; null = firmware uses its built-in default (typically 1440 = 24 h).
            $table->unsignedSmallInteger('dhcp_lease_duration')->nullable()->after('dhcp_end');
        });
    }

    public function down(): void
    {
        Schema::table('location_networks', function (Blueprint $table) {
            $table->dropColumn('dhcp_lease_duration');
        });
    }
};
