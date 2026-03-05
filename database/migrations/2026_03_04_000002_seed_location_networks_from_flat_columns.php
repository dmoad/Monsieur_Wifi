<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Seeds location_networks from existing flat columns in location_settings.
 *
 * For each location_settings row this creates:
 *   sort_order=0  type=captive_portal  ← captive_portal_* columns
 *   sort_order=1  type=password        ← password_wifi_* columns
 *
 * The old flat columns in location_settings are left untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        $rows = DB::table('location_settings')->get();

        foreach ($rows as $s) {
            // --- Captive Portal network ---
            DB::table('location_networks')->insert([
                'location_id'       => $s->location_id,
                'sort_order'        => 0,
                'type'              => 'captive_portal',
                'enabled'           => (bool) ($s->captive_portal_enabled ?? false),
                'ssid'              => $s->captive_portal_ssid ?? 'MrWiFi Guest',
                'visible'           => (bool) ($s->captive_portal_visible ?? true),
                'vlan_id'           => $s->captive_portal_vlan ?? null,
                'vlan_tagging'      => $s->captive_portal_vlan_tagging ?? 'disabled',

                // captive-portal specific
                'auth_method'       => $s->captive_auth_method ?? 'click-through',
                'portal_password'   => $s->captive_portal_password ?? null,
                'social_auth_method'=> $s->captive_social_auth_method ?? null,
                'session_timeout'   => $s->session_timeout ?? 60,
                'idle_timeout'      => $s->idle_timeout ?? 15,
                'redirect_url'      => $s->captive_portal_redirect ?? $s->redirect_url ?? null,
                'portal_design_id'  => $s->portal_design_id ?? null,
                'download_limit'    => $s->download_limit ?? null,
                'upload_limit'      => $s->upload_limit ?? null,

                // IP / DHCP
                'ip_mode'           => 'static',
                'ip_address'        => $s->captive_portal_ip ?? '192.168.2.1',
                'netmask'           => $s->captive_portal_netmask ?? '255.255.255.0',
                'gateway'           => $s->captive_portal_gateway ?? null,
                'dns1'              => $s->captive_portal_dns1 ?? '8.8.8.8',
                'dns2'              => $s->captive_portal_dns2 ?? '8.8.4.4',
                'dhcp_enabled'      => (bool) ($s->captive_portal_dhcp_enabled ?? true),
                'dhcp_start'        => $s->captive_portal_dhcp_start ?? '192.168.2.100',
                'dhcp_end'          => $s->captive_portal_dhcp_end ?? '192.168.2.200',

                // MAC filtering — use captive-specific list if available, fall back to general
                'mac_filter_mode'   => $s->mac_filter_mode ?? 'allow-all',
                'mac_filter_list'   => $s->captive_mac_filter_list ?? $s->mac_filter_list ?? null,

                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // --- Password WiFi network ---
            DB::table('location_networks')->insert([
                'location_id'       => $s->location_id,
                'sort_order'        => 1,
                'type'              => 'password',
                'enabled'           => (bool) ($s->password_wifi_enabled ?? true),
                'ssid'              => $s->password_wifi_ssid ?? 'monsieur-wifi',
                'visible'           => (bool) ($s->wifi_visible ?? true),
                'vlan_id'           => $s->password_wifi_vlan ?? null,
                'vlan_tagging'      => $s->password_wifi_vlan_tagging ?? 'disabled',

                // password specific
                'password'          => $s->password_wifi_password ?? 'abcd1234',
                'security'          => $s->password_wifi_security ?? 'wpa2-psk',
                'cipher_suites'     => $s->password_wifi_cipher_suites ?? 'CCMP',

                // IP / DHCP
                'ip_mode'           => $s->password_wifi_ip_mode ?? 'static',
                'ip_address'        => $s->password_wifi_ip ?? '192.168.1.1',
                'netmask'           => $s->password_wifi_netmask ?? '255.255.255.0',
                'gateway'           => $s->password_wifi_gateway ?? null,
                'dns1'              => $s->password_wifi_dns1 ?? '8.8.8.8',
                'dns2'              => $s->password_wifi_dns2 ?? '8.8.4.4',
                'dhcp_enabled'      => (bool) ($s->password_wifi_dhcp_enabled ?? true),
                'dhcp_start'        => $s->password_wifi_dhcp_start ?? '192.168.1.100',
                'dhcp_end'          => $s->password_wifi_dhcp_end ?? '192.168.1.200',

                // MAC filtering — use secured-specific list if available
                'mac_filter_mode'   => $s->mac_filter_mode ?? 'allow-all',
                'mac_filter_list'   => $s->secured_mac_filter_list ?? $s->mac_filter_list ?? null,

                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Remove seeded rows — identified by matching location_ids from location_settings
        $locationIds = DB::table('location_settings')->pluck('location_id');
        DB::table('location_networks')->whereIn('location_id', $locationIds)->delete();
    }
};
