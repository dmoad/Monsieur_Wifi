<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wifi_stat_clients', function (Blueprint $table) {
            $table->id();

            // Parent snapshot
            $table->foreignId('wifi_stat_id')->constrained('wifi_stats')->cascadeOnDelete();

            // AP-reported timestamp copied from parent for direct time-range
            // queries without joining wifi_stats
            $table->datetime('ap_ts');

            // Denormalized FKs for fast filtering on all four lookup axes
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('zone_id')->nullable()->constrained('zones')->nullOnDelete();

            // Resolved at insert by matching slot.ssid → location_networks.ssid
            // (zone-aware: uses primary location's networks for zone members)
            // null when the SSID has no LocationNetwork entry (e.g. the wan slot)
            $table->foreignId('location_network_id')->nullable()->constrained('location_networks')->nullOnDelete();

            // Resolved at insert: GuestNetworkUser WHERE mac_address = mac AND network_id = location_network_id
            // null for unregistered devices or when location_network_id is null
            $table->foreignId('guest_network_user_id')->nullable()->constrained('guest_network_users')->nullOnDelete();

            // Slot context — repeated per row for direct analytics without joins
            $table->tinyInteger('slot')->unsigned();
            $table->string('ssid', 255);
            $table->string('nasid', 100);
            $table->string('network', 100);
            $table->string('radio', 10);   // radio0 | radio1
            $table->string('iface', 20);
            $table->string('band', 3);     // 2g | 5g

            // Client identity
            $table->string('mac', 17);
            $table->string('ip', 45)->nullable();

            // Signal quality
            $table->smallInteger('signal_dbm');
            $table->smallInteger('signal_avg_dbm');
            $table->smallInteger('snr_db')->nullable();

            // Cumulative TX counters (since client associated; reset on reassociation)
            $table->unsignedInteger('tx_retries');
            $table->unsignedInteger('tx_failed');

            // Session timing
            $table->unsignedInteger('connected_time_s');
            $table->unsignedInteger('inactive_time_ms');

            // No timestamps() — ap_ts is the temporal anchor;
            // WifiStatClient uses $timestamps = false for bulk insert() support

            // Composite indexes for the primary analytics query patterns
            $table->index(['location_id', 'ap_ts']);
            $table->index(['zone_id', 'ap_ts']);
            $table->index('mac');
            $table->index('location_network_id');
            $table->index('guest_network_user_id');
            $table->index('wifi_stat_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wifi_stat_clients');
    }
};
