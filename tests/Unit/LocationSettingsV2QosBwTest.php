<?php

namespace Tests\Unit;

use App\Models\LocationSettingsV2;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LocationSettingsV2QosBwTest extends TestCase
{
    #[Test]
    public function class_sum_within_half_of_both_wan_legs_succeeds(): void
    {
        LocationSettingsV2::assertQosClassSumWithinHalfWan([
            'wan_down_kbps' => 1_000_000,
            'wan_up_kbps'   => 1_000_000,
            'voip_bw'       => 100_000,
            'streaming_bw'  => 100_000,
            'be_bw'         => 100_000,
            'bulk_bw'       => 200_000,
        ]);
        $this->assertTrue(true);
    }

    #[Test]
    public function class_sum_exceeding_half_of_download_fails(): void
    {
        $this->expectException(ValidationException::class);

        LocationSettingsV2::assertQosClassSumWithinHalfWan([
            'wan_down_kbps' => 1_000_000,
            'wan_up_kbps'   => 2_000_000,
            'voip_bw'       => 300_000,
            'streaming_bw'  => 200_000,
            'be_bw'         => 1_000,
            'bulk_bw'       => 0,
        ]);
    }

    #[Test]
    public function class_sum_exceeding_half_of_upload_fails(): void
    {
        $this->expectException(ValidationException::class);

        LocationSettingsV2::assertQosClassSumWithinHalfWan([
            'wan_down_kbps' => 2_000_000,
            'wan_up_kbps'   => 1_000_000,
            'voip_bw'       => 300_000,
            'streaming_bw'  => 200_000,
            'be_bw'         => 1_000,
            'bulk_bw'       => 0,
        ]);
    }

    #[Test]
    public function sum_equal_to_half_of_smaller_leg_succeeds(): void
    {
        $wan = 1_000_000;
        $half = 500_000;
        LocationSettingsV2::assertQosClassSumWithinHalfWan([
            'wan_down_kbps' => $wan,
            'wan_up_kbps'   => $wan,
            'voip_bw'       => $half,
            'streaming_bw'  => 0,
            'be_bw'         => 0,
            'bulk_bw'       => 0,
        ]);
        $this->assertTrue(true);
    }

    #[Test]
    public function nonzero_class_sum_with_zero_wan_succeeds_pending_wan_setup(): void
    {
        // WAN not yet declared — half-of-WAN can't be enforced; accept the values
        // and let the assertion re-run once WAN capacity is filled in.
        LocationSettingsV2::assertQosClassSumWithinHalfWan([
            'wan_down_kbps' => 0,
            'wan_up_kbps'   => 0,
            'voip_bw'       => 1,
            'streaming_bw'  => 0,
            'be_bw'         => 0,
            'bulk_bw'       => 0,
        ]);
        $this->assertTrue(true);
    }
}
