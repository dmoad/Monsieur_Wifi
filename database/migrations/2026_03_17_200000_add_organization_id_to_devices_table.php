<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
        });

        // Backfill: assign each device to its owner's current organization
        DB::statement('
            UPDATE devices d
            JOIN users u ON d.owner_id = u.id
            SET d.organization_id = u.current_organization_id
            WHERE u.current_organization_id IS NOT NULL
        ');

        // Backfill: assign locations without org to their owner's current org
        DB::statement('
            UPDATE locations l
            JOIN users u ON l.owner_id = u.id
            SET l.organization_id = u.current_organization_id
            WHERE l.organization_id IS NULL AND u.current_organization_id IS NOT NULL
        ');

        // Backfill: assign zones without org to their owner's current org
        DB::statement('
            UPDATE zones z
            JOIN users u ON z.owner_id = u.id
            SET z.organization_id = u.current_organization_id
            WHERE z.organization_id IS NULL AND u.current_organization_id IS NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organization_id');
        });
    }
};
