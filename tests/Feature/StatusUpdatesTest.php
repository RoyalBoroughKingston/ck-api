<?php

namespace Tests\Feature;

use App\Models\Referral;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

class StatusUpdatesTest extends TestCase
{
    /*
     * List all of the status updates.
     */

    public function test_guest_cannot_list_them()
    {
        $referral = factory(Referral::class)->create();

        $response = $this->json('GET', "/core/v1/status-updates?filter[referral_id]={$referral->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_can_list_them()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);
        $referral = factory(Referral::class)->create(['service_id' => $service->id]);
        $referral->statusUpdates()->create([
            'user_id' => $user->id,
            'from' => $referral->status,
            'to' => Referral::STATUS_IN_PROGRESS,
            'comments' => 'Assigned to me',
        ]);

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/status-updates?filter[referral_id]={$referral->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'referral_id' => $referral->id,
            'user_id' => $user->id,
            'from' => $referral->status,
            'to' => Referral::STATUS_IN_PROGRESS,
            'comments' => 'Assigned to me',
        ]);
    }
}
