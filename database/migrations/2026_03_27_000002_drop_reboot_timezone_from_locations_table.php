<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn('scheduled_reboot_timezone');
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->string('scheduled_reboot_timezone', 64)->nullable()->default('UTC')->after('scheduled_reboot_time');
        });
    }
};
