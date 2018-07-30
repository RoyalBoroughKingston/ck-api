<?php

namespace Tests\Feature;

use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuditsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_list_them()
    {
        $response = $this->json('GET', '/core/v1/audits');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
