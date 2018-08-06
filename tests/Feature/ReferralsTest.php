<?php

namespace Tests\Feature;

use App\Models\Referral;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ReferralsTest extends TestCase
{
    /*
     * List all the referrals.
     */

    public function test_guest_cannot_list_them()
    {
        $response = $this->json('GET', '/core/v1/referrals');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_for_another_service_cannot_list_them()
    {
        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);
        $anotherService = factory(Service::class)->create();

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/referrals?filter[service_id]={$anotherService->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_worker_can_list_them()
    {
        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);
        $referral = factory(Referral::class)->create([
            'service_id' => $service->id,
            'email' => $this->faker->safeEmail,
            'comments' => $this->faker->paragraph,
            'referral_consented_at' => $this->now,
            'referee_name' => $this->faker->name,
            'referee_email' => $this->faker->safeEmail,
            'referee_phone' => $this->faker->phoneNumber,
            'organisation' => $this->faker->company,
        ]);

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/referrals?filter[service_id]={$service->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            [
                'id' => $referral->id,
                'service_id' => $referral->service_id,
                'reference' => $referral->reference,
                'status' => $referral->status,
                'name' => $referral->name,
                'email' => $referral->email,
                'phone' => $referral->phone,
                'other_contact' => $referral->other_contact,
                'postcode_outward_code' => $referral->postcode_outward_code,
                'comments' => $referral->comments,
                'referral_consented_at' => $referral->referral_consented_at->format(Carbon::ISO8601),
                'feedback_consented_at' => null,
                'referee_name' => $referral->referee_name,
                'referee_email' => $referral->referee_email,
                'referee_phone' => $referral->referee_phone,
                'referee_organisation' => $referral->organisation,
                'created_at' => $referral->created_at->format(Carbon::ISO8601),
                'updated_at' => $referral->updated_at->format(Carbon::ISO8601),
            ]
        ]);
    }

    public function test_service_worker_cannot_list_without_service_id()
    {
        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);

        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/referrals');

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
    
    /*
     * Create a referral.
     */

    public function test_guest_can_create_referral()
    {
        $service = factory(Service::class)->create();

        $payload = [
            'service_id' => $service->id,
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'phone' => null,
            'other_contact' => null,
            'postcode_outward_code' => null,
            'comments' => null,
            'referral_consented' => true,
            'feedback_consented' => false,
            'referee_name' => $this->faker->name,
            'referee_email' => $this->faker->safeEmail,
            'referee_phone' => $this->faker->phoneNumber,
            'organisation' => $this->faker->company,
        ];

        $response = $this->json('POST', '/core/v1/referrals', $payload);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment([
            'service_id' => $payload['service_id'],
            'name' => $payload['name'],
            'email' => $payload['email'],
            'phone' => $payload['phone'],
            'other_contact' => $payload['other_contact'],
            'postcode_outward_code' => $payload['postcode_outward_code'],
            'comments' => $payload['comments'],
            'referee_name' => $payload['referee_name'],
            'referee_email' => $payload['referee_email'],
            'referee_phone' => $payload['referee_phone'],
            'referee_organisation' => $payload['organisation'],
        ]);
    }
    
    /*
     * Get a specific referral.
     */

    public function test_guest_cannot_view_one()
    {
        $referral = factory(Referral::class)->create();

        $response = $this->json('GET', "/core/v1/referrals/{$referral->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_for_another_service_cannot_view_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);
        $referral = factory(Referral::class)->create();

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/referrals/{$referral->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_worker_can_view_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);
        $referral = factory(Referral::class)->create([
            'service_id' => $service->id,
            'referral_consented_at' => $this->now,
        ]);

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/referrals/{$referral->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $referral->id,
                'service_id' => $referral->service_id,
                'reference' => $referral->reference,
                'status' => Referral::STATUS_NEW,
                'name' => $referral->name,
                'email' => null,
                'phone' => null,
                'other_contact' => null,
                'postcode_outward_code' => null,
                'comments' => null,
                'referral_consented_at' => $this->now->format(Carbon::ISO8601),
                'feedback_consented_at' => null,
                'referee_name' => null,
                'referee_email' => null,
                'referee_phone' => null,
                'referee_organisation' => null,
                'created_at' => $referral->created_at->format(Carbon::ISO8601),
                'updated_at' => $referral->updated_at->format(Carbon::ISO8601),
            ]
        ]);
    }
}
