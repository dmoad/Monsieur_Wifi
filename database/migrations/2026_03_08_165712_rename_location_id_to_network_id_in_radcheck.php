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
        if ($schema->hasColumn('radcheck', 'location_id') && ! $schema->hasColumn('radcheck', 'network_id')) {
            Schema::connection('radius')->table('radcheck', function (Blueprint $table) {
                $table->renameColumn('location_id', 'network_id');
            });
        }
    }

    public function down(): void
    {
        $schema = Schema::connection('radius');
        if (! $schema->hasTable('radcheck')) {
            return;
        }
        if ($schema->hasColumn('radcheck', 'network_id') && ! $schema->hasColumn('radcheck', 'location_id')) {
            Schema::connection('radius')->table('radcheck', function (Blueprint $table) {
                $table->renameColumn('network_id', 'location_id');
            });
        }
    }
};
