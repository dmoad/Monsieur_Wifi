<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * No database — avoids any migration side effects against a shared DB.
 */
class RadiusGuestSessionStatsAuthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['services.radius.stats_secret' => 'test-radius-secret']);
    }

    public function test_radius_guest_session_stats_returns_401_without_token(): void
    {
        $this->postJson('/api/radius/guest-session-stats', [
            'username' => 'aa:bb:cc:dd:ee:ff',
            'acct_session_id' => 'sess-1',
            'acct_status_type' => 1,
        ])->assertUnauthorized();
    }

    public function test_radius_guest_session_stats_returns_401_with_invalid_token(): void
    {
        $this->postJson('/api/radius/guest-session-stats', [
            'username' => 'aa:bb:cc:dd:ee:ff',
            'acct_session_id' => 'sess-1',
            'acct_status_type' => 1,
        ], [
            'Authorization' => 'Bearer wrong-secret',
        ])->assertUnauthorized();
    }
}
