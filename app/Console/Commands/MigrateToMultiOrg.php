<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\Location;
use App\Models\Organization;
use App\Models\User;
use App\Models\Zone;
use App\Models\CaptivePortalDesign;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MigrateToMultiOrg extends Command
{
    protected $signature = 'migrate:to-multi-org
        {--dry-run : Show what would happen without making changes}
        {--org-name=Digilan : Name of the shared organization to create}
        {--owner-email= : Email of the user who will own the shared org (must exist in DB)}
        {--skip-zitadel : Skip Zitadel user creation (if users will be migrated separately)}';

    protected $description = 'Migrate existing prod data to multi-org structure. Creates a shared org and assigns all devices/locations/zones to it.';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $orgName = $this->option('org-name');
        $ownerEmail = $this->option('owner-email');

        if ($dryRun) {
            $this->warn('=== DRY RUN MODE — no changes will be made ===');
        }

        // ── Step 0: Audit current state ─────────────────────────────────────
        $this->info('');
        $this->info('=== STEP 0: Audit current state ===');

        $totalUsers = User::count();
        $usersWithZitadel = User::whereNotNull('zitadel_sub')->count();
        $usersWithoutZitadel = User::whereNull('zitadel_sub')->count();
        $totalDevices = Device::count();
        $devicesWithOrg = Device::whereNotNull('organization_id')->count();
        $devicesWithoutOrg = Device::whereNull('organization_id')->count();
        $totalLocations = Location::count();
        $locationsWithOrg = Location::whereNotNull('organization_id')->count();
        $locationsWithoutOrg = Location::whereNull('organization_id')->count();
        $totalZones = Zone::count();
        $zonesWithOrg = Zone::whereNotNull('organization_id')->count();
        $zonesWithoutOrg = Zone::whereNull('organization_id')->count();
        $totalOrgs = Organization::count();
        $totalDesigns = CaptivePortalDesign::count();

        $this->table(['Metric', 'Count'], [
            ['Users (total)', $totalUsers],
            ['Users with Zitadel sub', $usersWithZitadel],
            ['Users without Zitadel sub', $usersWithoutZitadel],
            ['Organizations', $totalOrgs],
            ['Devices (total)', $totalDevices],
            ['Devices with org', $devicesWithOrg],
            ['Devices WITHOUT org', $devicesWithoutOrg],
            ['Locations (total)', $totalLocations],
            ['Locations with org', $locationsWithOrg],
            ['Locations WITHOUT org', $locationsWithoutOrg],
            ['Zones (total)', $totalZones],
            ['Zones with org', $zonesWithOrg],
            ['Zones WITHOUT org', $zonesWithoutOrg],
            ['Captive Portal Designs', $totalDesigns],
        ]);

        // Show users with their device/location counts
        $this->info('');
        $this->info('Users with devices or locations:');
        $users = User::withCount(['devices', 'locations'])->get();
        $activeUsers = $users->filter(fn($u) => $u->devices_count > 0 || $u->locations_count > 0);

        if ($activeUsers->isEmpty()) {
            $this->warn('No users with devices or locations found.');
        } else {
            $this->table(
                ['ID', 'Name', 'Email', 'Zitadel Sub', 'Devices', 'Locations', 'Has Org'],
                $activeUsers->map(fn($u) => [
                    $u->id,
                    $u->name,
                    $u->email,
                    $u->zitadel_sub ? Str::limit($u->zitadel_sub, 20) : '—',
                    $u->devices_count,
                    $u->locations_count,
                    $u->current_organization_id ? 'Yes' : 'No',
                ])->toArray()
            );
        }

        // If everything already has orgs, nothing to do
        if ($devicesWithoutOrg === 0 && $locationsWithoutOrg === 0 && $zonesWithoutOrg === 0) {
            $this->info('All resources already have organization_id set. Nothing to migrate.');
            return Command::SUCCESS;
        }

        if ($dryRun) {
            $this->info('');
            $this->info('=== DRY RUN COMPLETE — run without --dry-run to apply changes ===');
            return Command::SUCCESS;
        }

        // ── Step 1: Find or create the shared org ───────────────────────────
        $this->info('');
        $this->info('=== STEP 1: Create shared organization ===');

        // Find the owner
        if (! $ownerEmail) {
            $ownerEmail = $this->ask('Email of the user who will own the shared org');
        }

        $owner = User::where('email', $ownerEmail)->first();
        if (! $owner) {
            $this->error("User with email '{$ownerEmail}' not found.");
            return Command::FAILURE;
        }

        $this->info("Owner: {$owner->name} ({$owner->email})");

        // Create or find org
        $org = Organization::where('name', $orgName)->first();
        if ($org) {
            $this->warn("Organization '{$orgName}' already exists (ID: {$org->id}). Using it.");
        } else {
            if (! $this->confirm("Create organization '{$orgName}' owned by {$owner->email}?")) {
                $this->info('Aborted.');
                return Command::SUCCESS;
            }

            $org = Organization::create([
                'name' => $orgName,
                'slug' => Str::slug($orgName),
                'owner_id' => $owner->id,
                'plan' => 'enterprise',
            ]);
            $this->info("Created organization '{$orgName}' (ID: {$org->id}) with enterprise plan.");
        }

        // Set owner's current org
        if (! $owner->current_organization_id) {
            $owner->update(['current_organization_id' => $org->id]);
            $this->info("Set {$owner->email}'s current org to '{$orgName}'.");
        }

        // ── Step 2: Assign orphan devices → shared org ──────────────────────
        $this->info('');
        $this->info('=== STEP 2: Assign orphan devices to shared org ===');

        $orphanDevices = Device::whereNull('organization_id')->count();
        if ($orphanDevices > 0) {
            Device::whereNull('organization_id')->update(['organization_id' => $org->id]);
            $this->info("Assigned {$orphanDevices} devices to '{$orgName}'.");
        } else {
            $this->info('No orphan devices found.');
        }

        // ── Step 3: Assign orphan locations → shared org ────────────────────
        $this->info('');
        $this->info('=== STEP 3: Assign orphan locations to shared org ===');

        $orphanLocations = Location::whereNull('organization_id')->count();
        if ($orphanLocations > 0) {
            Location::whereNull('organization_id')->update(['organization_id' => $org->id]);
            $this->info("Assigned {$orphanLocations} locations to '{$orgName}'.");
        } else {
            $this->info('No orphan locations found.');
        }

        // ── Step 4: Assign orphan zones → shared org ────────────────────────
        $this->info('');
        $this->info('=== STEP 4: Assign orphan zones to shared org ===');

        $orphanZones = Zone::whereNull('organization_id')->count();
        if ($orphanZones > 0) {
            Zone::whereNull('organization_id')->update(['organization_id' => $org->id]);
            $this->info("Assigned {$orphanZones} zones to '{$orgName}'.");
        } else {
            $this->info('No orphan zones found.');
        }

        // ── Step 5: Assign orphan captive portal designs → shared org ───────
        $this->info('');
        $this->info('=== STEP 5: Assign orphan captive portal designs to shared org ===');

        $orphanDesigns = CaptivePortalDesign::whereNull('organization_id')->count();
        if ($orphanDesigns > 0) {
            CaptivePortalDesign::whereNull('organization_id')->update(['organization_id' => $org->id]);
            $this->info("Assigned {$orphanDesigns} captive portal designs to '{$orgName}'.");
        } else {
            $this->info('No orphan captive portal designs found.');
        }

        // ── Step 6: Set all users without an org to the shared org ──────────
        $this->info('');
        $this->info('=== STEP 6: Assign users without org to shared org ===');

        $usersWithoutOrg = User::whereNull('current_organization_id')->count();
        if ($usersWithoutOrg > 0) {
            User::whereNull('current_organization_id')->update(['current_organization_id' => $org->id]);
            $this->info("Assigned {$usersWithoutOrg} users to '{$orgName}'.");
        } else {
            $this->info('All users already have an org.');
        }

        // ── Summary ─────────────────────────────────────────────────────────
        $this->info('');
        $this->info('=== MIGRATION COMPLETE ===');
        $this->table(['Resource', 'Migrated'], [
            ['Devices', $orphanDevices],
            ['Locations', $orphanLocations],
            ['Zones', $orphanZones],
            ['Captive Portal Designs', $orphanDesigns],
            ['Users (assigned to org)', $usersWithoutOrg],
        ]);

        $this->info('');
        $this->warn('NEXT STEPS:');
        $this->line('  1. Run database migrations:  php artisan migrate');
        $this->line("  2. Assign authz owner role to {$owner->email} on org {$org->id}");
        $this->line('     (via authz gRPC or HTTP API)');
        $this->line('  3. Create Zitadel accounts for real users (via Nexus invite API)');
        $this->line('  4. Update users.zitadel_sub with their Zitadel user IDs');
        $this->line('  5. Assign authz roles for each user on the shared org');
        $this->line('');

        return Command::SUCCESS;
    }
}
