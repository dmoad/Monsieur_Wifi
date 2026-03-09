<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('location_networks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();

            // Display order (0-indexed)
            $table->unsignedInteger('sort_order')->default(0);

            // Network type: password | captive_portal | open
            $table->string('type')->default('password');

            $table->boolean('enabled')->default(true);
            $table->string('ssid');
            $table->boolean('visible')->default(true);

            // VLAN
            $table->unsignedInteger('vlan_id')->nullable();
            $table->string('vlan_tagging')->default('disabled');

            // --- Password WiFi fields (type = password) ---
            $table->string('password')->nullable();
            $table->string('security')->default('wpa2-psk');
            $table->string('cipher_suites')->default('CCMP');

            // --- Captive Portal fields (type = captive_portal) ---
            $table->string('auth_method')->default('click-through');
            $table->string('portal_password')->nullable();
            $table->string('social_auth_method')->nullable();
            $table->unsignedInteger('session_timeout')->default(60);
            $table->unsignedInteger('idle_timeout')->default(15);
            $table->string('redirect_url')->nullable();
            $table->foreignId('portal_design_id')->nullable()->constrained('captive_portal_designs')->nullOnDelete();
            $table->unsignedInteger('download_limit')->nullable();
            $table->unsignedInteger('upload_limit')->nullable();

            // --- Shared IP / DHCP fields (all types) ---
            $table->string('ip_mode')->default('static');
            $table->string('ip_address')->nullable();
            $table->string('netmask')->default('255.255.255.0');
            $table->string('gateway')->nullable();
            $table->string('dns1')->default('8.8.8.8');
            $table->string('dns2')->default('8.8.4.4');
            $table->boolean('dhcp_enabled')->default(true);
            $table->string('dhcp_start')->nullable();
            $table->string('dhcp_end')->nullable();

            // --- Per-network MAC filtering ---
            $table->string('mac_filter_mode')->default('allow-all');
            $table->json('mac_filter_list')->nullable();

            $table->timestamps();

            $table->index(['location_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_networks');
    }
};
