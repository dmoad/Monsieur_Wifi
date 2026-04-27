<?php

namespace Tests\Unit;

use App\Models\LocationQosDomain;
use App\Models\QosClass;
use Tests\TestCase;

class LocationQosDomainModelTest extends TestCase
{
    public function test_qos_constants_and_model_exist(): void
    {
        $this->assertTrue(class_exists(LocationQosDomain::class));
        $this->assertEquals('BE', QosClass::BE);
    }
}
