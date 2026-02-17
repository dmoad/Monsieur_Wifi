<?php

namespace Database\Seeders;

use App\Models\BlockedDomain;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlockedDomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories
        $adultCategory = Category::where('slug', 'adult-content')->first();
        $gamblingCategory = Category::where('slug', 'gambling')->first();
        $malwareCategory = Category::where('slug', 'malware')->first();
        $socialCategory = Category::where('slug', 'social-media')->first();
        $customCategory = Category::where('slug', 'custom-list')->first();

        $domains = [
            // Adult Content
            [
                'domain' => 'adultsite.example.com',
                'category_id' => $adultCategory->id,
                'notes' => 'Added for content filtering purposes.',
                'block_subdomains' => true,
                'is_active' => true,
                'source' => 'manual',
                'created_at' => now()->subDays(30),
            ],
            [
                'domain' => 'explicit.example.org',
                'category_id' => $adultCategory->id,
                'notes' => 'Known adult content site.',
                'block_subdomains' => true,
                'is_active' => true,
                'source' => 'import',
                'created_at' => now()->subDays(25),
            ],

            // Gambling
            [
                'domain' => 'casino.example.com',
                'category_id' => $gamblingCategory->id,
                'notes' => 'Online casino website.',
                'block_subdomains' => true,
                'is_active' => true,
                'source' => 'manual',
                'created_at' => now()->subDays(45),
                'updated_at' => now()->subDays(20),
            ],
            [
                'domain' => 'betsite.example.net',
                'category_id' => $gamblingCategory->id,
                'notes' => 'Sports betting platform.',
                'block_subdomains' => true,
                'is_active' => true,
                'source' => 'import',
                'created_at' => now()->subDays(40),
            ],

            // Malware
            [
                'domain' => 'malware.example.com',
                'category_id' => $malwareCategory->id,
                'notes' => 'Known malware distribution site.',
                'block_subdomains' => true,
                'is_active' => true,
                'source' => 'api',
                'created_at' => now()->subDays(10),
            ],
            [
                'domain' => 'phishing.example.org',
                'category_id' => $malwareCategory->id,
                'notes' => 'Phishing website targeting users.',
                'block_subdomains' => true,
                'is_active' => true,
                'source' => 'api',
                'created_at' => now()->subDays(15),
            ],

            // Social Media
            [
                'domain' => 'social.example.com',
                'category_id' => $socialCategory->id,
                'notes' => 'Social media platform.',
                'block_subdomains' => true,
                'is_active' => true,
                'source' => 'manual',
                'created_at' => now()->subDays(5),
            ],

            // Custom List
            [
                'domain' => 'custom-block.example.com',
                'category_id' => $customCategory->id,
                'notes' => 'Custom blocked domain by administrator.',
                'block_subdomains' => true,
                'is_active' => true,
                'source' => 'manual',
                'created_at' => now()->subDays(2),
            ],
            [
                'domain' => 'test.blocked-site.com',
                'category_id' => $customCategory->id,
                'notes' => 'Test domain for blocking functionality.',
                'block_subdomains' => false,
                'is_active' => true,
                'source' => 'manual',
                'created_at' => now()->subDays(1),
            ],
        ];

        foreach ($domains as $domainData) {
            BlockedDomain::updateOrCreate(
                [
                    'domain' => $domainData['domain'],
                    'category_id' => $domainData['category_id']
                ],
                $domainData
            );
        }

        // Add some additional random domains for testing
        $this->addRandomDomains($adultCategory->id, 1020); // To make it ~1024 total
        $this->addRandomDomains($gamblingCategory->id, 854); // To make it ~856 total
        $this->addRandomDomains($malwareCategory->id, 2343); // To make it ~2345 total
        $this->addRandomDomains($socialCategory->id, 341); // To make it ~342 total
        $this->addRandomDomains($customCategory->id, 41); // To make it ~43 total
    }

    private function addRandomDomains($categoryId, $count)
    {
        $domains = [];
        $prefixes = ['www', 'app', 'api', 'mail', 'shop', 'blog', 'news', 'forum', 'test', 'dev'];
        $baseNames = ['example', 'test', 'demo', 'sample', 'mock', 'fake', 'dummy', 'temp'];
        $tlds = ['com', 'org', 'net', 'info', 'biz', 'co.uk', 'de', 'fr'];
        
        // Track used domains to avoid duplicates within this batch
        $usedDomains = [];
        
        // Get existing domains for this category to avoid duplicates
        $existingDomains = BlockedDomain::where('category_id', $categoryId)
            ->pluck('domain')
            ->toArray();
        $usedDomains = array_flip($existingDomains);

        $attempts = 0;
        $maxAttempts = $count * 10; // Safety limit to prevent infinite loop
        
        for ($i = 0; $i < $count && $attempts < $maxAttempts; $attempts++) {
            $prefix = $prefixes[array_rand($prefixes)];
            $baseName = $baseNames[array_rand($baseNames)];
            $number = rand(1, 99999); // Increased range to reduce collision probability
            $tld = $tlds[array_rand($tlds)];

            $domain = "{$prefix}{$number}.{$baseName}.{$tld}";

            // Skip if domain already exists
            if (isset($usedDomains[$domain])) {
                continue;
            }
            
            $usedDomains[$domain] = true;

            $domains[] = [
                'domain' => $domain,
                'category_id' => $categoryId,
                'notes' => 'Auto-generated test domain',
                'block_subdomains' => rand(0, 1) == 1,
                'is_active' => rand(0, 10) > 1, // 90% active
                'source' => ['manual', 'import', 'api'][array_rand(['manual', 'import', 'api'])],
                'created_at' => now()->subDays(rand(1, 365)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ];
            
            $i++;

            // Insert in batches of 100 to avoid memory issues
            if (count($domains) >= 100) {
                BlockedDomain::insert($domains);
                $domains = [];
            }
        }

        // Insert remaining domains
        if (!empty($domains)) {
            BlockedDomain::insert($domains);
        }
    }
}
