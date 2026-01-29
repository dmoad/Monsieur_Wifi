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
        Schema::connection('radius')->table('radcheck', function (Blueprint $table) {
            if (!Schema::connection('radius')->hasColumn('radcheck', 'idle_timeout')) {
                $table->integer('idle_timeout')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('radius')->table('radcheck', function (Blueprint $table) {
            $table->dropColumn('idle_timeout');
        });
    }
};
