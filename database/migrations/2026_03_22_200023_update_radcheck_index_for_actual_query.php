<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'radius';

    public function up(): void
    {
        $schema = Schema::connection('radius');
        if (! $schema->hasTable('radcheck')) {
            return;
        }

        $indexNames = array_column($schema->getIndexes('radcheck'), 'name');
        if (in_array('idx_radcheck_user_network_expiry', $indexNames, true)) {
            return;
        }

        $schema->table('radcheck', function (Blueprint $table) use ($indexNames) {
            if (in_array('idx_radcheck_user_network_zone_expiry', $indexNames, true)) {
                $table->dropIndex('idx_radcheck_user_network_zone_expiry');
            }

            // Composite index optimised for:
            // SELECT * FROM radcheck WHERE username=? AND network_id=? AND expiration_time > NOW()+30s
            //
            // Column order rationale:
            //   1. username        — highest cardinality equality; eliminates the most rows immediately
            //   2. network_id      — equality; further narrows the set
            //   3. expiration_time — range (>); must be last so the B-tree prefix covers all equalities first
            //
            // zone_id is NOT in this index because it is not used in the query predicate.
            $table->index(
                ['username', 'network_id', 'expiration_time'],
                'idx_radcheck_user_network_expiry'
            );
        });
    }

    public function down(): void
    {
        Schema::connection('radius')->table('radcheck', function (Blueprint $table) {
            $table->dropIndex('idx_radcheck_user_network_expiry');

            $table->index(
                ['username', 'network_id', 'zone_id', 'expiration_time'],
                'idx_radcheck_user_network_zone_expiry'
            );
        });
    }
};
