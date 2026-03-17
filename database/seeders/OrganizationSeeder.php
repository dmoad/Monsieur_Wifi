<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Organization;
use App\Models\User;
use App\Models\Zone;
use App\Services\AuthzClient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class OrganizationSeeder extends Seeder
{
    /**
     * Seed realistic multi-org scenario with all role types.
     *
     * Creates:
     *  - 4 organizations (restaurant, hotel, café, partner company)
     *  - 8 users with various roles
     *  - Zones, locations linked to orgs
     *  - Cross-org partner access
     *
     * All users have password: "password123"
     * All users have fake zitadel_sub values (prefix with "seed_")
     */
    public function run(): void
    {
        $this->command->info('Seeding organizations, users, zones, locations, and RBAC roles...');

        $password = Hash::make('password123');

        // ─── Users ───────────────────────────────────────────────────────

        $nathan = User::updateOrCreate(
            ['email' => 'nathan@digilan.fr'],
            [
                'name'              => 'Nathan Fourrier',
                'password'          => $password,
                'zitadel_sub'       => 'seed_nathan',
                'role'              => 'user',
                'email_verified_at' => now(),
            ]
        );

        $marie = User::updateOrCreate(
            ['email' => 'marie@restaurant-du-port.fr'],
            [
                'name'              => 'Marie Dupont',
                'password'          => $password,
                'zitadel_sub'       => 'seed_marie',
                'role'              => 'user',
                'email_verified_at' => now(),
            ]
        );

        $jean = User::updateOrCreate(
            ['email' => 'jean@restaurant-du-port.fr'],
            [
                'name'              => 'Jean Martin',
                'password'          => $password,
                'zitadel_sub'       => 'seed_jean',
                'role'              => 'user',
                'email_verified_at' => now(),
            ]
        );

        $sophie = User::updateOrCreate(
            ['email' => 'sophie@hotel-bellevue.fr'],
            [
                'name'              => 'Sophie Bernard',
                'password'          => $password,
                'zitadel_sub'       => 'seed_sophie',
                'role'              => 'user',
                'email_verified_at' => now(),
            ]
        );

        $lucas = User::updateOrCreate(
            ['email' => 'lucas@hotel-bellevue.fr'],
            [
                'name'              => 'Lucas Moreau',
                'password'          => $password,
                'zitadel_sub'       => 'seed_lucas',
                'role'              => 'user',
                'email_verified_at' => now(),
            ]
        );

        $emma = User::updateOrCreate(
            ['email' => 'emma@cafe-central.fr'],
            [
                'name'              => 'Emma Leroy',
                'password'          => $password,
                'zitadel_sub'       => 'seed_emma',
                'role'              => 'user',
                'email_verified_at' => now(),
            ]
        );

        $thomas = User::updateOrCreate(
            ['email' => 'thomas@netpro-install.fr'],
            [
                'name'              => 'Thomas Petit',
                'password'          => $password,
                'zitadel_sub'       => 'seed_thomas',
                'role'              => 'user',
                'email_verified_at' => now(),
            ]
        );

        $camille = User::updateOrCreate(
            ['email' => 'camille@netpro-install.fr'],
            [
                'name'              => 'Camille Roux',
                'password'          => $password,
                'zitadel_sub'       => 'seed_camille',
                'role'              => 'user',
                'email_verified_at' => now(),
            ]
        );

        // ─── Organizations ───────────────────────────────────────────────

        $restaurantOrg = Organization::updateOrCreate(
            ['slug' => 'restaurant-du-port'],
            ['name' => 'Restaurant du Port', 'owner_id' => $marie->id]
        );

        $hotelOrg = Organization::updateOrCreate(
            ['slug' => 'hotel-bellevue'],
            ['name' => 'Hôtel Bellevue', 'owner_id' => $sophie->id]
        );

        $cafeOrg = Organization::updateOrCreate(
            ['slug' => 'cafe-central'],
            ['name' => 'Café Central', 'owner_id' => $emma->id]
        );

        $partnerOrg = Organization::updateOrCreate(
            ['slug' => 'netpro-install'],
            ['name' => 'NetPro Install', 'owner_id' => $thomas->id]
        );

        // Set current org for each user
        $marie->update(['current_organization_id' => $restaurantOrg->id]);
        $jean->update(['current_organization_id' => $restaurantOrg->id]);
        $sophie->update(['current_organization_id' => $hotelOrg->id]);
        $lucas->update(['current_organization_id' => $hotelOrg->id]);
        $emma->update(['current_organization_id' => $cafeOrg->id]);
        $thomas->update(['current_organization_id' => $partnerOrg->id]);
        $camille->update(['current_organization_id' => $partnerOrg->id]);
        $nathan->update(['current_organization_id' => $restaurantOrg->id]);

        // ─── Zones ──────────────────────────────────────────────────────

        $restaurantZone = Zone::updateOrCreate(
            ['name' => 'Salle & Terrasse', 'owner_id' => $marie->id],
            ['description' => 'Zone principale du restaurant', 'organization_id' => $restaurantOrg->id, 'is_active' => true]
        );

        $hotelLobbyZone = Zone::updateOrCreate(
            ['name' => 'Lobby & Common Areas', 'owner_id' => $sophie->id],
            ['description' => 'Hall, bar, piscine', 'organization_id' => $hotelOrg->id, 'is_active' => true]
        );

        $hotelRoomsZone = Zone::updateOrCreate(
            ['name' => 'Chambres', 'owner_id' => $sophie->id],
            ['description' => 'Étages 1 à 4', 'organization_id' => $hotelOrg->id, 'is_active' => true]
        );

        $cafeZone = Zone::updateOrCreate(
            ['name' => 'Espace Client', 'owner_id' => $emma->id],
            ['description' => 'Salle et comptoir', 'organization_id' => $cafeOrg->id, 'is_active' => true]
        );

        // ─── Locations ──────────────────────────────────────────────────

        Location::updateOrCreate(
            ['name' => 'Salle principale', 'owner_id' => $marie->id],
            [
                'address' => '12 Quai des Pêcheurs', 'city' => 'Marseille', 'country' => 'France',
                'postal_code' => '13001', 'status' => 'active',
                'organization_id' => $restaurantOrg->id, 'zone_id' => $restaurantZone->id,
            ]
        );

        Location::updateOrCreate(
            ['name' => 'Terrasse', 'owner_id' => $marie->id],
            [
                'address' => '12 Quai des Pêcheurs', 'city' => 'Marseille', 'country' => 'France',
                'postal_code' => '13001', 'status' => 'active',
                'organization_id' => $restaurantOrg->id, 'zone_id' => $restaurantZone->id,
            ]
        );

        Location::updateOrCreate(
            ['name' => 'Hall d\'accueil', 'owner_id' => $sophie->id],
            [
                'address' => '45 Boulevard de la Mer', 'city' => 'Nice', 'country' => 'France',
                'postal_code' => '06000', 'status' => 'active',
                'organization_id' => $hotelOrg->id, 'zone_id' => $hotelLobbyZone->id,
            ]
        );

        Location::updateOrCreate(
            ['name' => 'Bar & Piscine', 'owner_id' => $sophie->id],
            [
                'address' => '45 Boulevard de la Mer', 'city' => 'Nice', 'country' => 'France',
                'postal_code' => '06000', 'status' => 'active',
                'organization_id' => $hotelOrg->id, 'zone_id' => $hotelLobbyZone->id,
            ]
        );

        Location::updateOrCreate(
            ['name' => 'Étage 1 — Chambres 101-110', 'owner_id' => $sophie->id],
            [
                'address' => '45 Boulevard de la Mer', 'city' => 'Nice', 'country' => 'France',
                'postal_code' => '06000', 'status' => 'active',
                'organization_id' => $hotelOrg->id, 'zone_id' => $hotelRoomsZone->id,
            ]
        );

        Location::updateOrCreate(
            ['name' => 'Étage 2 — Chambres 201-210', 'owner_id' => $sophie->id],
            [
                'address' => '45 Boulevard de la Mer', 'city' => 'Nice', 'country' => 'France',
                'postal_code' => '06000', 'status' => 'active',
                'organization_id' => $hotelOrg->id, 'zone_id' => $hotelRoomsZone->id,
            ]
        );

        Location::updateOrCreate(
            ['name' => 'Café Central', 'owner_id' => $emma->id],
            [
                'address' => '8 Place de la République', 'city' => 'Lyon', 'country' => 'France',
                'postal_code' => '69001', 'status' => 'active',
                'organization_id' => $cafeOrg->id, 'zone_id' => $cafeZone->id,
            ]
        );

        // ─── RBAC Roles via AuthZ ────────────────────────────────────────
        // Role IDs from config/rbac.php:
        //   1 = owner, 2 = admin, 3 = operator, 4 = viewer, 5 = partner

        $orgTarget = config('rbac.targets.org', 'mrwifi:org');

        $assignments = [
            // Restaurant du Port
            ['sub' => 'seed_marie',   'role' => 1, 'target' => $orgTarget, 'target_id' => $restaurantOrg->id],  // owner
            ['sub' => 'seed_jean',    'role' => 3, 'target' => $orgTarget, 'target_id' => $restaurantOrg->id],  // operator
            ['sub' => 'seed_nathan',  'role' => 2, 'target' => $orgTarget, 'target_id' => $restaurantOrg->id],  // admin (you, for testing)

            // Hôtel Bellevue
            ['sub' => 'seed_sophie',  'role' => 1, 'target' => $orgTarget, 'target_id' => $hotelOrg->id],       // owner
            ['sub' => 'seed_lucas',   'role' => 4, 'target' => $orgTarget, 'target_id' => $hotelOrg->id],       // viewer

            // Café Central
            ['sub' => 'seed_emma',    'role' => 1, 'target' => $orgTarget, 'target_id' => $cafeOrg->id],        // owner

            // NetPro Install (partner company)
            ['sub' => 'seed_thomas',  'role' => 1, 'target' => $orgTarget, 'target_id' => $partnerOrg->id],     // owner of own org
            ['sub' => 'seed_camille', 'role' => 3, 'target' => $orgTarget, 'target_id' => $partnerOrg->id],     // operator in own org

            // Partner cross-org access
            ['sub' => 'seed_thomas',  'role' => 5, 'target' => $orgTarget, 'target_id' => $restaurantOrg->id],  // partner on restaurant
            ['sub' => 'seed_thomas',  'role' => 5, 'target' => $orgTarget, 'target_id' => $hotelOrg->id],       // partner on hotel
            ['sub' => 'seed_camille', 'role' => 5, 'target' => $orgTarget, 'target_id' => $restaurantOrg->id],  // partner on restaurant
        ];

        try {
            $authz = app(AuthzClient::class);

            foreach ($assignments as $a) {
                $authz->assignRole($a['sub'], $a['role'], $a['target'], (string) $a['target_id']);
                $this->command->info("  ✓ {$a['sub']} → role {$a['role']} on {$a['target']}:{$a['target_id']}");
            }
        } catch (\Exception $e) {
            $this->command->warn("  ⚠ Authz service unreachable — skipping role assignments: {$e->getMessage()}");
            $this->command->warn("    Run `php artisan db:seed --class=OrganizationSeeder` again when authz is up.");
        }

        // ─── Summary ────────────────────────────────────────────────────

        $this->command->newLine();
        $this->command->info('╔══════════════════════════════════════════════════════════════╗');
        $this->command->info('║  Seeded Multi-Org Data                                      ║');
        $this->command->info('╠══════════════════════════════════════════════════════════════╣');
        $this->command->info('║                                                              ║');
        $this->command->info('║  Organizations:                                              ║');
        $this->command->info("║    #{$restaurantOrg->id} Restaurant du Port  (owner: marie)             ║");
        $this->command->info("║    #{$hotelOrg->id} Hôtel Bellevue      (owner: sophie)            ║");
        $this->command->info("║    #{$cafeOrg->id} Café Central        (owner: emma)              ║");
        $this->command->info("║    #{$partnerOrg->id} NetPro Install      (owner: thomas)            ║");
        $this->command->info('║                                                              ║');
        $this->command->info('║  Users (password: password123):                              ║');
        $this->command->info('║    nathan@digilan.fr         → admin on Restaurant           ║');
        $this->command->info('║    marie@restaurant-du-port  → owner of Restaurant           ║');
        $this->command->info('║    jean@restaurant-du-port   → operator on Restaurant        ║');
        $this->command->info('║    sophie@hotel-bellevue     → owner of Hotel                ║');
        $this->command->info('║    lucas@hotel-bellevue      → viewer on Hotel               ║');
        $this->command->info('║    emma@cafe-central         → owner of Café (solo, no sub)  ║');
        $this->command->info('║    thomas@netpro-install     → partner on Restaurant + Hotel ║');
        $this->command->info('║    camille@netpro-install    → partner on Restaurant         ║');
        $this->command->info('║                                                              ║');
        $this->command->info('║  Scenarios you can test:                                     ║');
        $this->command->info('║    • Marie: full owner, manages her restaurant               ║');
        $this->command->info('║    • Jean: operator, day-to-day at the restaurant            ║');
        $this->command->info('║    • Thomas: partner, switches between 3 orgs (own + 2)     ║');
        $this->command->info('║    • Lucas: viewer, can only read hotel data                 ║');
        $this->command->info('║    • Emma: solo owner, no subscription (free tier)           ║');
        $this->command->info('║    • Nathan: admin access to restaurant for testing          ║');
        $this->command->info('║                                                              ║');
        $this->command->info('╚══════════════════════════════════════════════════════════════╝');
    }
}
