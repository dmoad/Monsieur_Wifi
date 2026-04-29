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
            $table->foreignId('network_id')
                ->nullable()
                ->after('location_id')
                ->constrained('location_networks')
                ->nullOnDelete();
            $table->unsignedBigInteger('session_duration')->nullable()->after('disconnect_time');
            $table->unsignedBigInteger('total_upload')->nullable()->after('total_download');
        });

        Schema::table('user_device_login_sessions', function (Blueprint $table) {
            $table->index('radius_session_id');
            $table->index(['mac_address', 'disconnect_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_device_login_sessions', function (Blueprint $table) {
            $table->dropIndex(['mac_address', 'disconnect_time']);
            $table->dropIndex(['radius_session_id']);
            $table->dropConstrainedForeignId('network_id');
            $table->dropColumn(['session_duration', 'total_upload']);
        });
    }
};
