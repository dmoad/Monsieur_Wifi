<?php

namespace Database\Seeders;

use App\Models\BlockedDomain;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BlockedDomainSeeder extends Seeder
{
    public function run(): void
    {
        $cat = Category::pluck('id', 'slug');

        $domains = [
            // Adult Content
            ['domain' => 'pornhub.com',       'category_id' => $cat['adult-content'], 'notes' => 'Major adult content platform.',         'block_subdomains' => true,  'is_active' => true, 'source' => 'manual'],
            ['domain' => 'xvideos.com',        'category_id' => $cat['adult-content'], 'notes' => 'Adult video streaming site.',           'block_subdomains' => true,  'is_active' => true, 'source' => 'manual'],
            ['domain' => 'xnxx.com',           'category_id' => $cat['adult-content'], 'notes' => 'Adult content website.',                'block_subdomains' => true,  'is_active' => true, 'source' => 'import'],
            ['domain' => 'redtube.com',        'category_id' => $cat['adult-content'], 'notes' => 'Adult video platform.',                 'block_subdomains' => true,  'is_active' => true, 'source' => 'import'],

            // Gambling
            ['domain' => 'bet365.com',         'category_id' => $cat['gambling'],      'notes' => 'Major online sports betting platform.', 'block_subdomains' => true,  'is_active' => true, 'source' => 'manual'],
            ['domain' => 'pokerstars.com',      'category_id' => $cat['gambling'],      'notes' => 'Online poker and casino.',              'block_subdomains' => true,  'is_active' => true, 'source' => 'manual'],
            ['domain' => 'draftkings.com',      'category_id' => $cat['gambling'],      'notes' => 'Daily fantasy sports and betting.',     'block_subdomains' => false, 'is_active' => true, 'source' => 'import'],
            ['domain' => 'fanduel.com',         'category_id' => $cat['gambling'],      'notes' => 'Sports betting and fantasy sports.',    'block_subdomains' => false, 'is_active' => true, 'source' => 'import'],

            // Malware
            ['domain' => 'malwaredomains.com', 'category_id' => $cat['malware'],       'notes' => 'Known malware distribution domain.',    'block_subdomains' => true,  'is_active' => true, 'source' => 'api'],
            ['domain' => 'virusspot.net',       'category_id' => $cat['malware'],       'notes' => 'Flagged malware hosting site.',         'block_subdomains' => true,  'is_active' => true, 'source' => 'api'],
            ['domain' => 'phishingsite.ru',     'category_id' => $cat['malware'],       'notes' => 'Active phishing campaign domain.',      'block_subdomains' => true,  'is_active' => true, 'source' => 'api'],
            ['domain' => 'trojanloader.xyz',    'category_id' => $cat['malware'],       'notes' => 'Trojan payload delivery domain.',       'block_subdomains' => true,  'is_active' => true, 'source' => 'api'],

            // Social Media
            ['domain' => 'facebook.com',        'category_id' => $cat['social-media'],  'notes' => 'Facebook social network.',              'block_subdomains' => true,  'is_active' => true, 'source' => 'manual'],
            ['domain' => 'instagram.com',       'category_id' => $cat['social-media'],  'notes' => 'Instagram photo sharing platform.',     'block_subdomains' => true,  'is_active' => true, 'source' => 'manual'],
            ['domain' => 'tiktok.com',          'category_id' => $cat['social-media'],  'notes' => 'TikTok short-form video platform.',     'block_subdomains' => true,  'is_active' => true, 'source' => 'manual'],
            ['domain' => 'twitter.com',         'category_id' => $cat['social-media'],  'notes' => 'Twitter / X social platform.',          'block_subdomains' => false, 'is_active' => true, 'source' => 'manual'],

            // Streaming
            ['domain' => 'netflix.com',         'category_id' => $cat['streaming'],     'notes' => 'Netflix video streaming service.',      'block_subdomains' => true,  'is_active' => true, 'source' => 'manual'],
            ['domain' => 'youtube.com',         'category_id' => $cat['streaming'],     'notes' => 'YouTube video platform.',               'block_subdomains' => true,  'is_active' => true, 'source' => 'manual'],
            ['domain' => 'twitch.tv',           'category_id' => $cat['streaming'],     'notes' => 'Live game streaming platform.',         'block_subdomains' => false, 'is_active' => true, 'source' => 'manual'],
            ['domain' => 'disneyplus.com',      'category_id' => $cat['streaming'],     'notes' => 'Disney+ streaming service.',            'block_subdomains' => true,  'is_active' => true, 'source' => 'import'],

            // Custom List
            ['domain' => 'ads.doubleclick.net', 'category_id' => $cat['custom-list'],   'notes' => 'Google ad network tracker.',            'block_subdomains' => true,  'is_active' => true, 'source' => 'manual'],
            ['domain' => 'tracking.hotjar.com', 'category_id' => $cat['custom-list'],   'notes' => 'User behaviour tracking service.',      'block_subdomains' => true,  'is_active' => true, 'source' => 'manual'],
            ['domain' => 'metrics.spotify.com', 'category_id' => $cat['custom-list'],   'notes' => 'Spotify telemetry endpoint.',           'block_subdomains' => false, 'is_active' => true, 'source' => 'manual'],
            ['domain' => 'telemetry.mozilla.org','category_id' => $cat['custom-list'],  'notes' => 'Firefox telemetry reporting domain.',   'block_subdomains' => false, 'is_active' => true, 'source' => 'import'],
        ];

        BlockedDomain::truncate();

        foreach ($domains as $data) {
            $data['created_at'] = now();
            $data['updated_at'] = now();
            BlockedDomain::create($data);
        }
    }
}
