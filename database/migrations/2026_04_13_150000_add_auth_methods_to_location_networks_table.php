<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('location_networks', function (Blueprint $table) {
            // Stores the ordered list of enabled login methods for captive portal networks,
            // e.g. ["click-through", "email", "social"]. When null, falls back to auth_method.
            $table->json('auth_methods')->nullable()->after('auth_method');
        });
    }

    public function down(): void
    {
        Schema::table('location_networks', function (Blueprint $table) {
            $table->dropColumn('auth_methods');
        });
    }
};
