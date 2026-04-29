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
        Schema::table('guest_network_users', function (Blueprint $table) {
            if (! Schema::hasColumn('guest_network_users', 'os')) {
                $table->string('os', 255)->nullable()->after('phone');
            }
            if (! Schema::hasColumn('guest_network_users', 'device_type')) {
                $table->string('device_type', 32)->nullable()->after('os');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guest_network_users', function (Blueprint $table) {
            if (Schema::hasColumn('guest_network_users', 'device_type')) {
                $table->dropColumn('device_type');
            }
            if (Schema::hasColumn('guest_network_users', 'os')) {
                $table->dropColumn('os');
            }
        });
    }
};
