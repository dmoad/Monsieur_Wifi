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
        Schema::table('user_device_login_sessions', function (Blueprint $table) {
            $table->timestamp('last_update_time')->nullable()->after('session_duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_device_login_sessions', function (Blueprint $table) {
            $table->dropColumn('last_update_time');
        });
    }
};
