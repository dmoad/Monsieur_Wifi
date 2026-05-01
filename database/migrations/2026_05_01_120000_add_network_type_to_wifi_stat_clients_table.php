<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wifi_stat_clients', function (Blueprint $table) {
            $table->string('network_type', 64)->nullable()->after('network');
        });
    }

    public function down(): void
    {
        Schema::table('wifi_stat_clients', function (Blueprint $table) {
            $table->dropColumn('network_type');
        });
    }
};
