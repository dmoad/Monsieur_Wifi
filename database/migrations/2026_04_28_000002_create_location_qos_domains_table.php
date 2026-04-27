<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('location_qos_domains')) {
            Schema::create('location_qos_domains', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('location_id');
                $table->string('class_id', 10);
                $table->string('domain', 253);
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('location_id', 'lqd_location_fk')
                    ->references('id')->on('locations')->cascadeOnDelete();
                $table->foreign('class_id', 'lqd_class_fk')
                    ->references('id')->on('qos_classes');
                $table->unique(['location_id', 'class_id', 'domain'], 'lqd_location_class_domain_unique');
                $table->index(['location_id', 'class_id'], 'lqd_location_class_idx');
            });
        }

        // Migrate existing per-network rows → per-location, deduplicating (location_id, class_id, domain).
        if (Schema::hasTable('location_network_qos_domains') && Schema::hasTable('location_networks')) {
            $rows = DB::table('location_network_qos_domains as lnqd')
                ->join('location_networks as ln', 'ln.id', '=', 'lnqd.location_network_id')
                ->select('ln.location_id', 'lnqd.class_id', 'lnqd.domain')
                ->get();

            foreach ($rows as $row) {
                DB::table('location_qos_domains')->insertOrIgnore([
                    'location_id' => $row->location_id,
                    'class_id' => $row->class_id,
                    'domain' => $row->domain,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('location_qos_domains');
    }
};
