<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * One-time data migration: copies the columns that are still relevant
 * (radio, WAN, VLAN global toggle, web filtering) from the legacy
 * location_settings table into the new location_settings_v2 table.
 *
 * Rows that already exist in v2 are skipped (idempotent).
 */
return new class extends Migration
{
    public function up(): void
    {
        $rows = DB::table('location_settings')->get();

        foreach ($rows as $old) {
            $exists = DB::table('location_settings_v2')
                ->where('location_id', $old->location_id)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('location_settings_v2')->insert([
                'location_id' => $old->location_id,

                // Radio / Channel
                'country_code'      => $old->country_code      ?? 'US',
                'transmit_power_2g' => $old->transmit_power_2g ?? 15,
                'transmit_power_5g' => $old->transmit_power_5g ?? 17,
                'channel_2g'        => $old->channel_2g        ?? null,
                'channel_5g'        => $old->channel_5g        ?? null,
                'channel_width_2g'  => $old->channel_width_2g  ?? 40,
                'channel_width_5g'  => $old->channel_width_5g  ?? 80,

                // WAN
                'wan_enabled'            => $old->wan_enabled            ?? 1,
                'wan_connection_type'    => $old->wan_connection_type    ?? 'dhcp',
                'wan_ip_address'         => $old->wan_ip_address         ?? null,
                'wan_netmask'            => $old->wan_netmask            ?? null,
                'wan_gateway'            => $old->wan_gateway            ?? null,
                'wan_primary_dns'        => $old->wan_primary_dns        ?? null,
                'wan_secondary_dns'      => $old->wan_secondary_dns      ?? null,
                'wan_pppoe_username'     => $old->wan_pppoe_username     ?? null,
                'wan_pppoe_password'     => $old->wan_pppoe_password     ?? null,
                'wan_pppoe_service_name' => $old->wan_pppoe_service_name ?? null,
                'wan_mac_address'        => $old->wan_mac_address        ?? null,
                'wan_mtu'                => $old->wan_mtu                ?? 1500,
                'wan_nat_enabled'        => $old->wan_nat_enabled        ?? 1,

                // VLAN global toggle
                'vlan_enabled' => $old->vlan_enabled ?? 0,

                // Web filtering
                'web_filter_enabled'    => $old->web_filter_enabled    ?? 0,
                'web_filter_domains'    => $old->web_filter_domains    ?? null,
                'web_filter_categories' => property_exists($old, 'web_filter_categories')
                    ? $old->web_filter_categories
                    : null,

                'created_at' => $old->created_at ?? now(),
                'updated_at' => $old->updated_at ?? now(),
            ]);
        }
    }

    public function down(): void
    {
        // Remove v2 rows that were seeded from the old table
        $oldLocationIds = DB::table('location_settings')->pluck('location_id');
        DB::table('location_settings_v2')->whereIn('location_id', $oldLocationIds)->delete();
    }
};
