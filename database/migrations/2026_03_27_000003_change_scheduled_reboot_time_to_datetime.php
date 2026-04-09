<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            // Change from time to datetime to store a specific date+time for one-time reboot
            $table->dateTime('scheduled_reboot_time')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->time('scheduled_reboot_time')->nullable()->change();
        });
    }
};
