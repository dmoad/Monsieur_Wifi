<?php

namespace Tests\Feature;

use App\Models\GuestNetworkUser;
use App\Models\Location;
use App\Models\LocationNetwork;
use App\Models\UserDeviceLoginSession;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Rolls back INSERT/updates only — never migrate:fresh (which drops every table).
 * Requires `php artisan migrate` beforehand so sessions table exists (CI runs migrate before PHPUnit).
 */
class RadiusGuestSessionStatsAccountingTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var list<string>
     */
    protected $connectionsToTransact = ['mysql'];

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.radius.stats_secret' => 'test-radius-secret']);

        if (! Schema::connection('mysql')->hasTable('user_device_login_sessions')) {
            $this->markTestSkipped(
                'Run `php artisan migrate` so user_device_login_sessions exists (this test does not use migrate:fresh).'
            );
        }
    }

    public function test_accounting_flow_links_session_and_updates_octets_and_stop(): void
    {
        $location = Location::create(['name' => 'Test Location']);
        $network = LocationNetwork::create([
            'location_id' => $location->id,
            'sort_order' => 0,
            'type' => 'captive_portal',
            'ssid' => 'Guest',
            'download_limit' => 100,
            'upload_limit' => 50,
        ]);

        $guest = GuestNetworkUser::create([
            'mac_address' => 'aa:bb:cc:dd:ee:ff',
            'location_id' => $location->id,
            'network_id' => $network->id,
            'zone_id' => 0,
            'blocked' => false,
        ]);

        UserDeviceLoginSession::create([
            'guest_network_user_id' => $guest->id,
            'mac_address' => 'aa:bb:cc:dd:ee:ff',
            'location_id' => $location->id,
            'network_id' => $network->id,
            'zone_id' => 0,
            'login_type' => 'click-login',
        ]);

        $hdr = ['Authorization' => 'Bearer test-radius-secret'];

        $this->postJson('/api/radius/guest-session-stats', [
            'username' => 'aa-bb-cc-dd-ee-ff',
            'acct_session_id' => 'acct-abc-123',
            'acct_status_type' => 1,
            'location_id' => $location->id,
            'network_id' => $network->id,
        ], $hdr)->assertOk()->assertJsonPath('success', true);

        $this->assertDatabaseHas('user_device_login_sessions', [
            'radius_session_id' => 'acct-abc-123',
            'mac_address' => 'aa:bb:cc:dd:ee:ff',
        ]);

        $this->postJson('/api/radius/guest-session-stats', [
            'username' => 'aabbccddeeff',
            'acct_session_id' => 'acct-abc-123',
            'acct_status_type' => 3,
            'acct_output_octets' => 5000,
            'acct_input_octets' => 900,
            'acct_session_time' => 120,
        ], $hdr)->assertOk();

        $session = UserDeviceLoginSession::query()->where('radius_session_id', 'acct-abc-123')->first();
        $this->assertEquals(5000, $session->total_download);
        $this->assertEquals(900, $session->total_upload);
        $this->assertEquals(120, $session->session_duration);

        $this->postJson('/api/radius/guest-session-stats', [
            'username' => 'aa:bb:cc:dd:ee:ff',
            'acct_session_id' => 'acct-abc-123',
            'acct_status_type' => 'Accounting-Stop',
            'acct_output_octets' => 12000,
            'acct_input_octets' => 2000,
            'acct_session_time' => 3600,
            'acct_stop_time' => 1700000000,
        ], $hdr)->assertOk();

        $session->refresh();
        $this->assertNotNull($session->disconnect_time);
        $this->assertEquals(12000, $session->total_download);
        $this->assertEquals(2000, $session->total_upload);
    }

    public function test_acct_start_with_nas_id_creates_roaming_session(): void
    {
        $location = Location::create(['name' => 'Roaming Location']);
        $network  = LocationNetwork::create([
            'location_id'    => $location->id,
            'sort_order'     => 0,
            'type'           => 'captive_portal',
            'ssid'           => 'Roaming',
            'download_limit' => 100,
            'upload_limit'   => 50,
        ]);

        $nasId = "7-{$location->id}-{$network->id}";
        $hdr   = ['Authorization' => 'Bearer test-radius-secret'];

        $response = $this->postJson('/api/radius/guest-session-stats', [
            'username'         => 'FF-EE-DD-CC-BB-AA',
            'acct_session_id'  => 'roaming-session-001',
            'acct_status_type' => 'Accounting-Start',
            'acct_start_time'  => 1700000000,
            'nas_id'           => $nasId,
        ], $hdr);

        $response->assertOk()->assertJsonPath('success', true);
        $response->assertJsonPath('data.radius_session_id', 'roaming-session-001');

        $this->assertDatabaseHas('user_device_login_sessions', [
            'radius_session_id' => 'roaming-session-001',
            'mac_address'       => 'FF-EE-DD-CC-BB-AA',
            'location_id'       => $location->id,
            'network_id'        => $network->id,
            'zone_id'           => 7,
            'login_type'        => 'roaming',
            'login_success'     => true,
        ]);

        $session = UserDeviceLoginSession::query()
            ->where('radius_session_id', 'roaming-session-001')
            ->firstOrFail();

        $this->assertNotNull($session->guest_network_user_id);
        $this->assertNull($session->disconnect_time);
        $this->assertNotNull($session->connect_time);

        $this->assertDatabaseHas('guest_network_users', [
            'mac_address' => 'FF-EE-DD-CC-BB-AA',
            'location_id' => $location->id,
            'network_id'  => $network->id,
            'zone_id'     => 7,
        ]);
    }

    public function test_acct_start_copies_guest_to_new_location_when_zone_matches(): void
    {
        $locationA = Location::create(['name' => 'Zone Location A']);
        $locationB = Location::create(['name' => 'Zone Location B']);
        $networkB  = LocationNetwork::create([
            'location_id'    => $locationB->id,
            'sort_order'     => 0,
            'type'           => 'captive_portal',
            'ssid'           => 'Guest-B',
            'download_limit' => 100,
            'upload_limit'   => 50,
        ]);

        // Existing guest record is in locationA, same zone (5)
        $existingGuest = GuestNetworkUser::create([
            'mac_address' => 'AA-11-BB-22-CC-33',
            'location_id' => $locationA->id,
            'network_id'  => null,
            'zone_id'     => 5,
            'blocked'     => false,
            'email'       => 'roamer@example.com',
        ]);

        $nasId = "5-{$locationB->id}-{$networkB->id}";
        $hdr   = ['Authorization' => 'Bearer test-radius-secret'];

        $response = $this->postJson('/api/radius/guest-session-stats', [
            'username'         => 'AA-11-BB-22-CC-33',
            'acct_session_id'  => 'roaming-zone-copy-001',
            'acct_status_type' => 'Accounting-Start',
            'nas_id'           => $nasId,
        ], $hdr);

        $response->assertOk()->assertJsonPath('success', true);

        // A new GuestNetworkUser row must exist for locationB (the original is untouched)
        $this->assertDatabaseHas('guest_network_users', [
            'mac_address' => 'AA-11-BB-22-CC-33',
            'location_id' => $locationB->id,
            'network_id'  => $networkB->id,
            'zone_id'     => 5,
        ]);

        // Original guest row is unchanged
        $this->assertDatabaseHas('guest_network_users', [
            'id'          => $existingGuest->id,
            'location_id' => $locationA->id,
        ]);

        // Session uses the new (copied) guest and is linked to locationB
        $session = UserDeviceLoginSession::query()
            ->where('radius_session_id', 'roaming-zone-copy-001')
            ->firstOrFail();

        $this->assertNotEquals($existingGuest->id, $session->guest_network_user_id);
        $this->assertEquals($locationB->id, $session->location_id);
        $this->assertEquals(5, $session->zone_id);
        $this->assertEquals('roaming', $session->login_type);
    }

    public function test_acct_stop_with_no_existing_session_creates_roaming_session(): void
    {
        $location = Location::create(['name' => 'Stop Backfill Location']);
        $network  = LocationNetwork::create([
            'location_id'    => $location->id,
            'sort_order'     => 0,
            'type'           => 'captive_portal',
            'ssid'           => 'Stop-Guest',
            'download_limit' => 100,
            'upload_limit'   => 50,
        ]);

        $hdr = ['Authorization' => 'Bearer test-radius-secret'];

        $this->postJson('/api/radius/guest-session-stats', [
            'username'           => '11-22-33-44-55-66',
            'acct_session_id'    => 'backfill-stop-001',
            'acct_status_type'   => 'Accounting-Stop',
            'nas_id'             => "5-{$location->id}-{$network->id}",
            'acct_input_octets'  => 1225188,
            'acct_output_octets' => 289273,
            'acct_session_time'  => 164,
            'acct_stop_time'     => 1777454481,
        ], $hdr)->assertOk()->assertJsonPath('success', true);

        $session = UserDeviceLoginSession::query()
            ->where('radius_session_id', 'backfill-stop-001')
            ->firstOrFail();

        $this->assertEquals('roaming', $session->login_type);
        $this->assertNotNull($session->disconnect_time);
        $this->assertEquals(1225188, $session->total_upload);
        $this->assertEquals(289273, $session->total_download);
        $this->assertEquals(164, $session->session_duration);
    }

    public function test_acct_interim_with_no_existing_session_returns_404(): void
    {
        $hdr = ['Authorization' => 'Bearer test-radius-secret'];

        $this->postJson('/api/radius/guest-session-stats', [
            'username'         => '11-22-33-44-55-66',
            'acct_session_id'  => 'nonexistent-session-999',
            'acct_status_type' => 'Interim-Update',
        ], $hdr)->assertStatus(404)->assertJsonPath('success', false);
    }
}
