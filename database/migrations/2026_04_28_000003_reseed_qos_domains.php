<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Replace the global QoS seed list and push any new entries into every
 * existing location's domain list (insertOrIgnore — user-added custom
 * domains are never removed).
 */
return new class extends Migration
{
    private function canonicalDomains(): array
    {
        return [
            // EF — Real-time (VoIP / video calls)
            ['class_id' => 'EF', 'domain' => 'zoom.us'],
            ['class_id' => 'EF', 'domain' => '*.zoom.us'],
            ['class_id' => 'EF', 'domain' => 'teams.microsoft.com'],
            ['class_id' => 'EF', 'domain' => '*.teams.microsoft.com'],
            ['class_id' => 'EF', 'domain' => '*.skype.com'],
            ['class_id' => 'EF', 'domain' => 'stun.l.google.com'],
            // AF41 — Streaming
            ['class_id' => 'AF41', 'domain' => 'api.netflix.com'],
            ['class_id' => 'AF41', 'domain' => '*.nflxvideo.com'],
            ['class_id' => 'AF41', 'domain' => '*.nflximg.net'],
            ['class_id' => 'AF41', 'domain' => 'nflxso.net'],
            ['class_id' => 'AF41', 'domain' => 'youtube.com'],
            ['class_id' => 'AF41', 'domain' => '*.youtube.com'],
            ['class_id' => 'AF41', 'domain' => 'googlevideo.com'],
            ['class_id' => 'AF41', 'domain' => '*.googlevideo.com'],
        ];
    }

    private function staleDomains(): array
    {
        return [
            ['class_id' => 'EF',   'domain' => 'meet.google.com'],
            ['class_id' => 'EF',   'domain' => '*.webex.com'],
            ['class_id' => 'EF',   'domain' => '*.discord.com'],
            ['class_id' => 'AF41', 'domain' => '*.netflix.com'],
            ['class_id' => 'AF41', 'domain' => '*.nflxvideo.net'],
            ['class_id' => 'AF41', 'domain' => '*.hotstar.com'],
            ['class_id' => 'CS1',  'domain' => '*.dropbox.com'],
            ['class_id' => 'CS1',  'domain' => '*.onedrive.com'],
            ['class_id' => 'CS1',  'domain' => '*.drive.google.com'],
            ['class_id' => 'CS1',  'domain' => '*.amazonaws.com'],
        ];
    }

    public function up(): void
    {
        $now = now();
        $canonical = $this->canonicalDomains();

        // 1. Sync global seed table (replace with canonical list).
        if (Schema::hasTable('qos_class_domains')) {
            DB::table('qos_class_domains')->truncate();
            $rows = array_map(fn ($d) => array_merge($d, ['created_at' => $now]), $canonical);
            DB::table('qos_class_domains')->insert($rows);
        }

        if (Schema::hasTable('location_qos_domains')) {
            // 2. Remove stale auto-seeded entries that are no longer in the canonical list.
            foreach ($this->staleDomains() as $stale) {
                DB::table('location_qos_domains')
                    ->where('class_id', $stale['class_id'])
                    ->where('domain', $stale['domain'])
                    ->delete();
            }

            // 3. Push new canonical entries into every existing location (non-destructive).
            $locationIds = DB::table('locations')->pluck('id');
            foreach ($locationIds as $locationId) {
                foreach ($canonical as $entry) {
                    DB::table('location_qos_domains')->insertOrIgnore([
                        'location_id' => $locationId,
                        'class_id'    => $entry['class_id'],
                        'domain'      => $entry['domain'],
                        'created_at'  => $now,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        // Not reversible — domain lists are user data after seeding.
    }
};
