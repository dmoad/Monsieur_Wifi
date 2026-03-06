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
        Schema::table('location_networks', function (Blueprint $table) {
            $table->json('working_hours')->nullable()->after('upload_limit');
        });
    }

    public function down(): void
    {
        Schema::table('location_networks', function (Blueprint $table) {
            $table->dropColumn('working_hours');
        });
    }
};
