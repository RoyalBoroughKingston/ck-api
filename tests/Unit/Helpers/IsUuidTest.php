<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;

class IsUuidTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_tell_valid_uuids_from_invalid_uuids()
    {
        $goodUuids = ['123e4567-e89b-12d3-a456-426655440000',
            'c73bcdcc-2669-4bf6-81d3-e4ae73fb11fd',
            'C73BCDCC-2669-4Bf6-81d3-E4AE73FB11FD'];
        $badUuids = ['c73bcdcc-2669-4bf6-81d3-e4an73fb11fd',
            'c73bcdcc26694bf681d3e4ae73fb11fd',
            'definitely-not-a-uuid'];
        foreach ($goodUuids as $goodUuid) {
            $this->assertTrue(is_uuid($goodUuid));
        }
        foreach ($badUuids as $badUuid) {
            $this->assertFalse(is_uuid($badUuid));
        }
    }
}
