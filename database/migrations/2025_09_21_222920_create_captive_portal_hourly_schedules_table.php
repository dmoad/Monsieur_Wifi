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
        Schema::create('captive_portal_hourly_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id');
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            $table->tinyInteger('hour')->unsigned(); // 0-23
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['location_id', 'day_of_week', 'hour'], 'cp_hourly_schedule_idx');
            $table->unique(['location_id', 'day_of_week', 'hour'], 'cp_hourly_schedule_unique');
            
            // Foreign key constraint (assuming locations table exists)
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('captive_portal_hourly_schedules');
    }
};
