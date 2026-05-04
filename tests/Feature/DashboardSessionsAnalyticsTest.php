<?php

namespace Tests\Feature;

use App\Models\GuestNetworkUser;
use App\Models\Location;
use App\Models\User;
use App\Models\UserDeviceLoginSession;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DashboardSessionsAnalyticsTest extends TestCase
{
    use DatabaseTransactions;

    /** @var array<int, string> */
    protected $connectionsToTransact = ['mysql'];

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['user_device_login_sessions', 'guest_network_users', 'locations'] as $table) {
            if (! Schema::hasTable($table)) {
                $this->markTestSkipped('Run migrations before DashboardSessionsAnalyticsTest.');
            }
        }
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_dashboard_overview_and_analytics_use_sessions_visible_locations_only(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-15 14:00:00', config('app.timezone')));

        $owner = User::factory()->create();
        $stranger = User::factory()->create();

        $locMine = Location::create([
            'name' => 'Mine',
            'owner_id' => $owner->id,
        ]);

        $locMineB = Location::create([
            'name' => 'Mine B',
            'owner_id' => $owner->id,
        ]);

        $locOther = Location::create([
            'name' => 'Other',
            'owner_id' => $stranger->id,
        ]);

        $g1 = GuestNetworkUser::create([
            'mac_address' => 'aa-bb-cc-dd-ee-01',
            'location_id' => $locMine->id,
            'blocked' => false,
        ]);

        $g2 = GuestNetworkUser::create([
            'mac_address' => 'aa-bb-cc-dd-ee-02',
            'location_id' => $locMine->id,
            'blocked' => false,
        ]);

        $gOther = GuestNetworkUser::create([
            'mac_address' => 'ff-ff-cc-dd-ee-99',
            'location_id' => $locOther->id,
            'blocked' => false,
        ]);

        $gStale = GuestNetworkUser::create([
            'mac_address' => 'aa-bb-cc-dd-ee-03',
            'location_id' => $locMine->id,
            'blocked' => false,
        ]);

        $oneGb = 1024 ** 3;

        UserDeviceLoginSession::create([
            'guest_network_user_id' => $g1->id,
            'mac_address' => $g1->mac_address,
            'location_id' => $locMine->id,
            'login_type' => 'portal',
            'connect_time' => Carbon::parse('2026-06-15 08:00:00'),
            'disconnect_time' => null,
            'total_download' => $oneGb,
            'total_upload' => (int) round($oneGb * 0.5),
            'login_success' => true,
        ]);

        UserDeviceLoginSession::create([
            'guest_network_user_id' => $g1->id,
            'mac_address' => $g1->mac_address,
            'location_id' => $locMineB->id,
            'login_type' => 'portal',
            'connect_time' => Carbon::parse('2026-06-15 08:05:00'),
            'disconnect_time' => null,
            'login_success' => true,
        ]);

        UserDeviceLoginSession::create([
            'guest_network_user_id' => $g1->id,
            'mac_address' => $g1->mac_address,
            'location_id' => $locMine->id,
            'login_type' => 'portal',
            'connect_time' => Carbon::parse('2026-06-15 08:30:00'),
            'disconnect_time' => null,
            'login_success' => true,
        ]);

        UserDeviceLoginSession::create([
            'guest_network_user_id' => $gStale->id,
            'mac_address' => $gStale->mac_address,
            'location_id' => $locMine->id,
            'login_type' => 'portal',
            'connect_time' => Carbon::parse('2026-06-15 14:00:00', config('app.timezone'))->copy()->subHours(25),
            'disconnect_time' => null,
            'login_success' => true,
        ]);

        UserDeviceLoginSession::create([
            'guest_network_user_id' => $g2->id,
            'mac_address' => $g2->mac_address,
            'location_id' => $locMine->id,
            'login_type' => 'portal',
            'connect_time' => Carbon::parse('2026-06-15 09:00:00'),
            'disconnect_time' => Carbon::parse('2026-06-15 10:00:00'),
            'total_download' => 2 * $oneGb,
            'total_upload' => $oneGb,
            'login_success' => true,
        ]);

        UserDeviceLoginSession::create([
            'guest_network_user_id' => $gOther->id,
            'mac_address' => $gOther->mac_address,
            'location_id' => $locOther->id,
            'login_type' => 'portal',
            'connect_time' => Carbon::parse('2026-06-15 11:00:00'),
            'disconnect_time' => null,
            'total_download' => 99 * $oneGb,
            'total_upload' => $oneGb,
            'login_success' => true,
        ]);

        UserDeviceLoginSession::create([
            'guest_network_user_id' => $g2->id,
            'mac_address' => $g2->mac_address,
            'location_id' => $locMine->id,
            'login_type' => 'portal',
            'connect_time' => Carbon::parse('2026-06-15 12:00:00'),
            'disconnect_time' => Carbon::parse('2026-06-15 13:00:00'),
            'total_download' => 100,
            'total_upload' => 50,
            'login_success' => false,
        ]);

        $token = Auth::guard('api')->login($owner);

        $overview = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson('/api/dashboard/overview');

        $overview->assertOk()->assertJsonPath('success', true);

        $minePayload = collect($overview->json('data.locations.data'))->firstWhere('id', $locMine->id);
        $this->assertNotNull($minePayload);

        $mineBPayload = collect($overview->json('data.locations.data'))->firstWhere('id', $locMineB->id);
        $this->assertNotNull($mineBPayload);

        $this->assertSame(1, $overview->json('data.network_stats.active_users'));
        $this->assertSame(1, $minePayload['users']);
        $this->assertSame(1, $mineBPayload['users']);

        $this->assertEqualsWithDelta(
            4.5,
            (float) $overview->json('data.network_stats.data_used_gb'),
            0.08,
            'network_stats today volume matches summed successful sessions (not truncated TB)'
        );

        $this->assertEqualsWithDelta(
            4.5,
            (float) $minePayload['data_usage_gb'],
            0.08,
            'Sum of download+upload bytes for successful sessions today on this location'
        );

        $this->assertSame(2, $minePayload['unique_users_today']);
        $this->assertSame(3, $minePayload['total_sessions']);

        $analytics = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson('/api/dashboard/analytics?period=7');

        $analytics->assertOk()
            ->assertJsonPath('data.analytics.total_users', 3)
            ->assertJsonPath('data.analytics.total_sessions', 5);

        $otherOverview = $this->withHeaders([
            'Authorization' => 'Bearer '.Auth::guard('api')->login($stranger),
            'Accept' => 'application/json',
        ])->getJson('/api/dashboard/overview');

        $otherOverview->assertOk()->assertJsonPath('data.network_stats.active_users', 1);
        $otherPayload = collect($otherOverview->json('data.locations.data'))->firstWhere('id', $locOther->id);
        $this->assertNotNull($otherPayload);
        $this->assertGreaterThan(90.0, (float) $otherPayload['data_usage_gb']);
    }

    public function test_data_usage_trends_daily_buckets(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-15 14:00:00', config('app.timezone')));

        $owner = User::factory()->create();

        $loc = Location::create([
            'name' => 'Trend Loc',
            'owner_id' => $owner->id,
        ]);

        $guest = GuestNetworkUser::create([
            'mac_address' => '11-22-33-44-55-66',
            'location_id' => $loc->id,
            'blocked' => false,
        ]);

        $halfGb = (int) round((1024 ** 3) / 2);

        UserDeviceLoginSession::create([
            'guest_network_user_id' => $guest->id,
            'mac_address' => $guest->mac_address,
            'location_id' => $loc->id,
            'login_type' => 'portal',
            'connect_time' => Carbon::parse('2026-06-14 10:00:00'),
            'disconnect_time' => Carbon::parse('2026-06-14 11:00:00'),
            'total_download' => $halfGb,
            'total_upload' => $halfGb,
            'login_success' => true,
        ]);

        UserDeviceLoginSession::create([
            'guest_network_user_id' => $guest->id,
            'mac_address' => $guest->mac_address,
            'location_id' => $loc->id,
            'login_type' => 'portal',
            'connect_time' => Carbon::parse('2026-06-15 08:00:00'),
            'disconnect_time' => Carbon::parse('2026-06-15 09:00:00'),
            'total_download' => $halfGb,
            'total_upload' => $halfGb,
            'login_success' => true,
        ]);

        $token = Auth::guard('api')->login($owner);

        $res = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson('/api/dashboard/data-usage-trends?period=7');

        $res->assertOk()->assertJsonPath('success', true);

        $daily = collect($res->json('data.daily_usage'));
        $june14 = $daily->firstWhere('date', '2026-06-14');
        $june15 = $daily->firstWhere('date', '2026-06-15');

        $this->assertNotNull($june14);
        $this->assertNotNull($june15);
        $this->assertEqualsWithDelta(0.5, (float) $june14['download_gb'], 0.06);
        $this->assertEqualsWithDelta(0.5, (float) $june14['upload_gb'], 0.06);
        $this->assertEqualsWithDelta(0.5, (float) $june15['download_gb'], 0.06);
        $this->assertEqualsWithDelta(0.5, (float) $june15['upload_gb'], 0.06);
    }
}
