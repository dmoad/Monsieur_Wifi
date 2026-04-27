<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('location_network_qos_domains')) {
            Schema::create('location_network_qos_domains', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('location_network_id');
                $table->string('class_id', 10);
                $table->string('domain', 253);
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('location_network_id', 'lnqd_network_fk')
                    ->references('id')->on('location_networks')->cascadeOnDelete();
                $table->foreign('class_id', 'lnqd_class_fk')
                    ->references('id')->on('qos_classes');
                $table->unique(['location_network_id', 'class_id', 'domain'], 'lnqd_network_class_domain_unique');
                $table->index(['location_network_id', 'class_id'], 'lnqd_network_class_idx');
            });
        }

        $networkIds = DB::table('location_networks')->pluck('id');
        if ($networkIds->isEmpty() || ! Schema::hasTable('qos_class_domains')) {
            return;
        }

        $globalRows = DB::table('qos_class_domains')->get();
        $now = now();
        foreach ($networkIds as $networkId) {
            foreach ($globalRows as $row) {
                if ($row->class_id === 'BE') {
                    continue;
                }
                DB::table('location_network_qos_domains')->insertOrIgnore([
                    'location_network_id' => $networkId,
                    'class_id' => $row->class_id,
                    'domain' => $row->domain,
                    'created_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('location_network_qos_domains');
    }
};
