<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill: convert existing integer values to string so data survives the column type change.
        DB::table('location_settings_v2')->whereNotNull('channel_2g')
            ->update(['channel_2g' => DB::raw('CAST(channel_2g AS CHAR)')]);
        DB::table('location_settings_v2')->whereNotNull('channel_5g')
            ->update(['channel_5g' => DB::raw('CAST(channel_5g AS CHAR)')]);

        Schema::table('location_settings_v2', function (Blueprint $table) {
            $table->string('channel_2g', 16)->nullable()->change();
            $table->string('channel_5g', 16)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Revert "auto" rows back to NULL (no valid integer), then cast the rest.
        DB::table('location_settings_v2')->where('channel_2g', 'auto')
            ->update(['channel_2g' => null]);
        DB::table('location_settings_v2')->where('channel_5g', 'auto')
            ->update(['channel_5g' => null]);

        Schema::table('location_settings_v2', function (Blueprint $table) {
            $table->integer('channel_2g')->nullable()->change();
            $table->integer('channel_5g')->nullable()->change();
        });
    }
};
