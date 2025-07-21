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
        Schema::table('captive_portal_designs', function (Blueprint $table) {
            $table->string('background_color_gradient_start')->nullable();
            $table->string('background_color_gradient_end')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('captive_portal_designs', function (Blueprint $table) {
            $table->dropColumn('background_color_gradient_start');
            $table->dropColumn('background_color_gradient_end');
        });
    }
};
