<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('radius')->table('radacct', function (Blueprint $table) {
            if (!Schema::connection('radius')->hasColumn('radacct', 'zone_id')) {
                $table->unsignedBigInteger('zone_id')->default(0)->after('location_id');
            }
            if (!Schema::connection('radius')->hasColumn('radacct', 'network_id')) {
                $table->unsignedBigInteger('network_id')->default(0)->after('zone_id');
            }
            $table->index(['network_id', 'zone_id'], 'idx_radacct_network_zone');
        });
    }

    public function down(): void
    {
        Schema::connection('radius')->table('radacct', function (Blueprint $table) {
            $table->dropIndex('idx_radacct_network_zone');
            $table->dropColumn(['zone_id', 'network_id']);
        });
    }
};
