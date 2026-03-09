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
            $table->renameColumn('location_id', 'network_id');
        });
    }

    public function down(): void
    {
        Schema::connection('radius')->table('radcheck', function (Blueprint $table) {
            $table->renameColumn('network_id', 'location_id');
        });
    }
};
