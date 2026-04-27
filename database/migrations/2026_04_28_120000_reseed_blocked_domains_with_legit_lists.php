<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Replaces all blocked_domains rows (including dummy seeder data) with
 * 10 real, category-appropriate example domains per category.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('blocked_domains') || ! Schema::hasTable('categories')) {
            return;
        }

        DB::table('blocked_domains')->delete();

        $bySlug = require database_path('data/blocked_domains_by_category.php');
        $now = now();
        $source = 'default_seed_v2';

        foreach ($bySlug as $slug => $domains) {
            $categoryId = DB::table('categories')->where('slug', $slug)->value('id');
            if (! $categoryId) {
                continue;
            }

            foreach ($domains as $domain) {
                DB::table('blocked_domains')->insertOrIgnore([
                    'domain' => $domain,
                    'category_id' => $categoryId,
                    'notes' => 'Default list entry (blocked_domains_by_category.php).',
                    'block_subdomains' => true,
                    'is_active' => true,
                    'source' => $source,
                    'metadata' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('blocked_domains')) {
            return;
        }

        DB::table('blocked_domains')->where('source', 'default_seed_v2')->delete();
    }
};
