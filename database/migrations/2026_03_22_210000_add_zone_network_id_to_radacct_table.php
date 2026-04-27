<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('radius');
        if (! $schema->hasTable('radacct')) {
            return;
        }

        $indexNames = array_column($schema->getIndexes('radacct'), 'name');

        Schema::connection('radius')->table('radacct', function (Blueprint $table) use ($schema, $indexNames) {
            if (! $schema->hasColumn('radacct', 'zone_id')) {
                $table->unsignedBigInteger('zone_id')->default(0)->after('location_id');
            }
            if (! $schema->hasColumn('radacct', 'network_id')) {
                $table->unsignedBigInteger('network_id')->default(0)->after('zone_id');
            }
            if (! in_array('idx_radacct_network_zone', $indexNames, true)) {
                $table->index(['network_id', 'zone_id'], 'idx_radacct_network_zone');
            }
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
