<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('location_networks', function (Blueprint $table) {
            $table->string('bridge_lan_dhcp_mode')->nullable()->default('dhcp_client')->after('ip_mode');
        });
    }

    public function down(): void
    {
        Schema::table('location_networks', function (Blueprint $table) {
            $table->dropColumn('bridge_lan_dhcp_mode');
        });
    }
};
