<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AuditsTest extends TestCase
{
    public function test_guest_cannot_list_them()
    {
        $response = $this->json('GET', '/core/v1/audits');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_list_them()
    {
        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);

        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/audits');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
