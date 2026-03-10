<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: add a temp unsignedBigInteger column alongside the existing varchar
        Schema::table('locations', function (Blueprint $table) {
            $table->unsignedBigInteger('device_id_new')->nullable()->after('device_id');
        });

        // Step 2: copy existing values, casting empty string to NULL
        DB::statement('UPDATE locations SET device_id_new = CAST(NULLIF(device_id, "") AS UNSIGNED)');

        // Step 3: drop the old varchar column
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn('device_id');
        });

        // Step 4: rename temp column to device_id
        Schema::table('locations', function (Blueprint $table) {
            $table->renameColumn('device_id_new', 'device_id');
        });

        // Step 5: add the foreign key constraint
        Schema::table('locations', function (Blueprint $table) {
            $table->foreign('device_id')
                  ->references('id')->on('devices')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        // Step 1: drop the FK
        Schema::table('locations', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
        });

        // Step 2: add temp varchar column alongside the integer
        Schema::table('locations', function (Blueprint $table) {
            $table->string('device_id_old')->nullable()->after('device_id');
        });

        // Step 3: copy values back to varchar
        DB::statement('UPDATE locations SET device_id_old = CAST(device_id AS CHAR)');

        // Step 4: drop the integer column
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn('device_id');
        });

        // Step 5: rename back to device_id
        Schema::table('locations', function (Blueprint $table) {
            $table->renameColumn('device_id_old', 'device_id');
        });
    }
};
