<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'radius';

    public function up(): void
    {
        Schema::connection('radius')->table('radcheck', function (Blueprint $table) {
            if (!Schema::connection('radius')->hasColumn('radcheck', 'zone_id')) {
                $table->unsignedBigInteger('zone_id')->default(0)->after('network_id');
            }

            // Composite index optimised for:
            // SELECT * FROM radcheck WHERE username=? AND network_id=? AND zone_id=? AND expiration_time > ?
            //
            // Column order rationale:
            //   1. username        — highest cardinality equality; eliminates the most rows immediately
            //   2. network_id      — equality; further narrows the set
            //   3. zone_id         — equality; further narrows the set
            //   4. expiration_time — range (>); must be last so the B-tree prefix covers all equalities first
            $table->index(
                ['username', 'network_id', 'zone_id', 'expiration_time'],
                'idx_radcheck_user_network_zone_expiry'
            );
        });
    }

    public function down(): void
    {
        Schema::connection('radius')->table('radcheck', function (Blueprint $table) {
            $table->dropIndex('idx_radcheck_user_network_zone_expiry');
            $table->dropColumn('zone_id');
        });
    }
};
