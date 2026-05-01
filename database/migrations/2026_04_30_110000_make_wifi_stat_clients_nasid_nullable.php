<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * AP payloads may send `"nasid": null` (alongside empty string "").
     * Nullable matches RADIUS / firmware where NAS-Identifier is absent.
     */
    public function up(): void
    {
        Schema::table('wifi_stat_clients', function (Blueprint $table) {
            $table->string('nasid', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('wifi_stat_clients', function (Blueprint $table) {
            $table->string('nasid', 100)->nullable(false)->change();
        });
    }
};
