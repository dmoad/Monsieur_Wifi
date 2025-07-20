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
        Schema::create('captive_portal_working_hours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id');
            $table->enum('day_of_week', [
                'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'
            ]);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->timestamps();

            $table->index(['location_id', 'day_of_week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('captive_portal_working_hours');
    }
};
