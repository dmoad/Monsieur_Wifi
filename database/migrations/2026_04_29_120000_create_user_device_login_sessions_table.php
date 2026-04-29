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
        Schema::create('user_device_login_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_network_user_id')->constrained('guest_network_users')->cascadeOnDelete();
            $table->string('mac_address', 255);
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();
            $table->unsignedBigInteger('zone_id')->default(0);
            $table->unsignedBigInteger('download_data')->nullable();
            $table->unsignedBigInteger('upload_data')->nullable();
            $table->string('login_type', 64);
            $table->string('radius_session_id', 128)->nullable();
            $table->timestamp('connect_time')->useCurrent();
            $table->timestamp('disconnect_time')->nullable();
            $table->unsignedBigInteger('total_download')->nullable();
            $table->boolean('login_success')->default(true);
            $table->timestamps();

            $table->index(['guest_network_user_id']);
            $table->index(['location_id']);
            $table->index(['mac_address']);
            $table->index(['login_type']);
            $table->index(['connect_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_device_login_sessions');
    }
};
