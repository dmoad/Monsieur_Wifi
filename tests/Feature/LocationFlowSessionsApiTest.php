<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LocationFlowSessionsApiTest extends TestCase
{
    use DatabaseTransactions;

    /** @var array<int, string> */
    protected $connectionsToTransact = ['mysql'];

    public function test_guest_receives_401(): void
    {
        if (! Schema::hasTable('flow_sessions')) {
            $this->markTestSkipped('Run migrations before LocationFlowSessionsApiTest.');
        }

        $this->getJson('/api/locations/999999/flow-sessions')
            ->assertStatus(401);
    }

    public function test_owner_can_list_and_paginate_flow_sessions(): void
    {
        if (! Schema::hasTable('flow_sessions')) {
            $this->markTestSkipped('Run migrations before LocationFlowSessionsApiTest.');
        }

        $uid = uniqid('fs', true);
        $device = Device::create([
            'name' => 'FS Test AP',
            'serial_number' => 'FS-'.$uid,
            'mac_address' => '00-11-22-33-'.substr(md5($uid), 0, 4),
            'device_key' => 'fskey'.$uid,
            'device_secret' => 'fssecret'.$uid,
        ]);

        $owner = User::factory()->create();

        $location = Location::create([
            'name' => 'FS Test Location',
            'owner_id' => $owner->id,
            'device_id' => $device->id,
        ]);

        $now = time();
        for ($i = 1; $i <= 7; $i++) {
            DB::table('flow_sessions')->insert([
                'device_id' => $device->id,
                'mac' => sprintf('AA-BB-CC-DD-EE-%02d', $i),
                'src_ip' => "192.168.1.{$i}",
                'dst_ip' => '8.8.8.8',
                'slot' => 0,
                'first_ts' => $now - 500 + $i,
                'last_ts' => $now + $i,
                'hits' => $i,
            ]);
        }

        $token = Auth::guard('api')->login($owner);

        $res = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson("/api/locations/{$location->id}/flow-sessions?page=1&per_page=5");

        $res->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.total', 7)
            ->assertJsonPath('data.last_page', 2)
            ->assertJsonPath('data.per_page', 5)
            ->assertJsonCount(5, 'data.data');

        $macRow = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson("/api/locations/{$location->id}/flow-sessions?search_field=mac&search=EE-03");

        $macRow->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.total', 1)
            ->assertJsonPath('data.data.0.mac', 'AA-BB-CC-DD-EE-03');

        $srcRow = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson("/api/locations/{$location->id}/flow-sessions?search_field=src_ip&search=192.168.1.5");

        $srcRow->assertOk()
            ->assertJsonPath('data.total', 1)
            ->assertJsonPath('data.data.0.src_ip', '192.168.1.5');

        $dstRow = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson("/api/locations/{$location->id}/flow-sessions?search_field=dst_ip&search=8.8.8.8");

        $dstRow->assertOk()
            ->assertJsonPath('data.total', 7);
    }

    public function test_other_user_gets_404(): void
    {
        if (! Schema::hasTable('flow_sessions')) {
            $this->markTestSkipped('Run migrations before LocationFlowSessionsApiTest.');
        }

        $uid = uniqid('nx', true);
        $device = Device::create([
            'name' => 'NX AP',
            'serial_number' => 'NX-'.$uid,
            'mac_address' => '01-11-22-33-'.substr(md5($uid), 0, 4),
            'device_key' => 'nxkey'.$uid,
            'device_secret' => 'nxsecret'.$uid,
        ]);

        $owner = User::factory()->create();
        $other = User::factory()->create();

        $location = Location::create([
            'name' => 'NX Location',
            'owner_id' => $owner->id,
            'device_id' => $device->id,
        ]);

        $token = Auth::guard('api')->login($other);

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson("/api/locations/{$location->id}/flow-sessions")
            ->assertStatus(404)
            ->assertJsonPath('success', false);
    }

    public function test_no_device_returns_empty_payload_with_message(): void
    {
        if (! Schema::hasTable('flow_sessions')) {
            $this->markTestSkipped('Run migrations before LocationFlowSessionsApiTest.');
        }

        $owner = User::factory()->create();

        $location = Location::create([
            'name' => 'No Device Loc',
            'owner_id' => $owner->id,
            'device_id' => null,
        ]);

        $token = Auth::guard('api')->login($owner);

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson("/api/locations/{$location->id}/flow-sessions")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.total', 0)
            ->assertJsonPath('data.message', 'No device assigned to this location.');
    }
}
