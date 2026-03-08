<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('location_settings_v2', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained()->onDelete('cascade');

            // ── Radio / Channel ──────────────────────────────────────────────
            $table->string('country_code', 2)->default('US');
            $table->integer('transmit_power_2g')->default(15);  // dBm
            $table->integer('transmit_power_5g')->default(17);  // dBm
            $table->integer('channel_2g')->nullable();
            $table->integer('channel_5g')->nullable();
            $table->integer('channel_width_2g')->default(40);   // MHz
            $table->integer('channel_width_5g')->default(80);   // MHz

            // ── WAN ──────────────────────────────────────────────────────────
            $table->boolean('wan_enabled')->default(true);
            $table->string('wan_connection_type')->default('dhcp'); // dhcp, static, pppoe
            $table->string('wan_ip_address')->nullable();
            $table->string('wan_netmask')->nullable();
            $table->string('wan_gateway')->nullable();
            $table->string('wan_primary_dns')->nullable();
            $table->string('wan_secondary_dns')->nullable();
            $table->string('wan_pppoe_username')->nullable();
            $table->string('wan_pppoe_password')->nullable();
            $table->string('wan_pppoe_service_name')->nullable();
            $table->string('wan_mac_address')->nullable();
            $table->integer('wan_mtu')->default(1500);
            $table->boolean('wan_nat_enabled')->default(true);

            // ── VLAN (global toggle — per-network IDs live in location_networks) ──
            $table->boolean('vlan_enabled')->default(false);

            // ── Web content filtering ────────────────────────────────────────
            $table->boolean('web_filter_enabled')->default(false);
            $table->json('web_filter_domains')->nullable();
            $table->json('web_filter_categories')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_settings_v2');
    }
};
