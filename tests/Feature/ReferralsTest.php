<?php

namespace Tests\Feature;

use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\Organisation;
use App\Models\Referral;
use App\Models\Service;
use App\Models\StatusUpdate;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
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

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['data' => []]);
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
            'referee_phone' => random_uk_phone(),
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

    public function test_only_referrals_user_is_authorised_to_view_are_shown()
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
            'referee_phone' => random_uk_phone(),
            'organisation' => $this->faker->company,
        ]);
        $anotherReferral = factory(Referral::class)->create();

        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/referrals');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $referral->id]);
        $response->assertJsonMissing(['id' => $anotherReferral->id]);
    }

    public function test_audit_created_when_listed()
    {
        $this->fakeEvents();

        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);

        Passport::actingAs($user);

        $this->json('GET', "/core/v1/referrals?filter[service_id]={$service->id}");

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($user) {
            return ($event->getAction() === Audit::ACTION_READ) &&
                ($event->getUser()->id === $user->id);
        });
    }

    public function test_global_admin_can_filter_by_organisation_name()
    {
        /**
         * @var \App\Models\Organisation $organisationOne
         * @var \App\Models\Service $serviceOne
         */
        $organisationOne = factory(Organisation::class)->create([
            'name' => 'Organisation One',
        ]);
        $serviceOne = factory(Service::class)->create([
            'organisation_id' => $organisationOne->id,
        ]);
        $referralOne = factory(Referral::class)->create([
            'service_id' => $serviceOne->id,
            'email' => $this->faker->safeEmail,
            'comments' => $this->faker->paragraph,
            'referral_consented_at' => $this->now,
            'referee_name' => $this->faker->name,
            'referee_email' => $this->faker->safeEmail,
            'referee_phone' => random_uk_phone(),
            'organisation' => $this->faker->company,
        ]);

        /**
         * @var \App\Models\Organisation $organisationTwo
         * @var \App\Models\Service $serviceTwo
         */
        $organisationTwo = factory(Organisation::class)->create([
            'name' => 'Organisation Two',
        ]);
        $serviceTwo = factory(Service::class)->create([
            'organisation_id' => $organisationTwo->id,
        ]);
        $referralTwo = factory(Referral::class)->create([
            'service_id' => $serviceTwo->id,
            'email' => $this->faker->safeEmail,
            'comments' => $this->faker->paragraph,
            'referral_consented_at' => $this->now,
            'referee_name' => $this->faker->name,
            'referee_email' => $this->faker->safeEmail,
            'referee_phone' => random_uk_phone(),
            'organisation' => $this->faker->company,
        ]);

        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/referrals?filter[organisation_name]={$organisationOne->name}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $referralOne->id]);
        $response->assertJsonMissing(['id' => $referralTwo->id]);
    }

    public function test_global_admin_can_filter_by_service_name()
    {
        /** @var \App\Models\Service $serviceOne */
        $serviceOne = factory(Service::class)->create([
            'name' => 'Service One',
        ]);
        $referralOne = factory(Referral::class)->create([
            'service_id' => $serviceOne->id,
            'email' => $this->faker->safeEmail,
            'comments' => $this->faker->paragraph,
            'referral_consented_at' => $this->now,
            'referee_name' => $this->faker->name,
            'referee_email' => $this->faker->safeEmail,
            'referee_phone' => random_uk_phone(),
            'organisation' => $this->faker->company,
        ]);

        /** @var \App\Models\Service $serviceTwo */
        $serviceTwo = factory(Service::class)->create([
            'name' => 'Service Two',
        ]);
        $referralTwo = factory(Referral::class)->create([
            'service_id' => $serviceTwo->id,
            'email' => $this->faker->safeEmail,
            'comments' => $this->faker->paragraph,
            'referral_consented_at' => $this->now,
            'referee_name' => $this->faker->name,
            'referee_email' => $this->faker->safeEmail,
            'referee_phone' => random_uk_phone(),
            'organisation' => $this->faker->company,
        ]);

        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/referrals?filter[service_name]={$serviceOne->name}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $referralOne->id]);
        $response->assertJsonMissing(['id' => $referralTwo->id]);
    }

    public function test_global_admin_can_sort_by_organisation_name()
    {
        /**
         * @var \App\Models\Organisation $organisationOne
         * @var \App\Models\Service $serviceOne
         */
        $organisationOne = factory(Organisation::class)->create([
            'name' => 'Organisation A',
        ]);
        $serviceOne = factory(Service::class)->create([
            'organisation_id' => $organisationOne->id,
        ]);
        $referralOne = factory(Referral::class)->create([
            'service_id' => $serviceOne->id,
            'email' => $this->faker->safeEmail,
            'comments' => $this->faker->paragraph,
            'referral_consented_at' => $this->now,
            'referee_name' => $this->faker->name,
            'referee_email' => $this->faker->safeEmail,
            'referee_phone' => random_uk_phone(),
            'organisation' => $this->faker->company,
        ]);

        /**
         * @var \App\Models\Organisation $organisationTwo
         * @var \App\Models\Service $serviceTwo
         */
        $organisationTwo = factory(Organisation::class)->create([
            'name' => 'Organisation B',
        ]);
        $serviceTwo = factory(Service::class)->create([
            'organisation_id' => $organisationTwo->id,
        ]);
        $referralTwo = factory(Referral::class)->create([
            'service_id' => $serviceTwo->id,
            'email' => $this->faker->safeEmail,
            'comments' => $this->faker->paragraph,
            'referral_consented_at' => $this->now,
            'referee_name' => $this->faker->name,
            'referee_email' => $this->faker->safeEmail,
            'referee_phone' => random_uk_phone(),
            'organisation' => $this->faker->company,
        ]);

        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/referrals?sort=-organisation_name');
        $data = $this->getResponseContent($response);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals($referralOne->id, $data['data'][1]['id']);
        $this->assertEquals($referralTwo->id, $data['data'][0]['id']);
    }

    public function test_global_admin_can_sort_by_service_name()
    {
        /** @var \App\Models\Service $serviceOne */
        $serviceOne = factory(Service::class)->create([
            'name' => 'Service A',
        ]);
        $referralOne = factory(Referral::class)->create([
            'service_id' => $serviceOne->id,
            'email' => $this->faker->safeEmail,
            'comments' => $this->faker->paragraph,
            'referral_consented_at' => $this->now,
            'referee_name' => $this->faker->name,
            'referee_email' => $this->faker->safeEmail,
            'referee_phone' => random_uk_phone(),
            'organisation' => $this->faker->company,
        ]);

        /** @var \App\Models\Organisation $organisationTwo */
        $serviceTwo = factory(Service::class)->create([
            'name' => 'Service B',
        ]);
        $referralTwo = factory(Referral::class)->create([
            'service_id' => $serviceTwo->id,
            'email' => $this->faker->safeEmail,
            'comments' => $this->faker->paragraph,
            'referral_consented_at' => $this->now,
            'referee_name' => $this->faker->name,
            'referee_email' => $this->faker->safeEmail,
            'referee_phone' => random_uk_phone(),
            'organisation' => $this->faker->company,
        ]);

        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/referrals?sort=-service_name');
        $data = $this->getResponseContent($response);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals($referralOne->id, $data['data'][1]['id']);
        $this->assertEquals($referralTwo->id, $data['data'][0]['id']);
    }

    public function test_can_append_status_last_updated_at_when_listed()
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
            'referee_phone' => random_uk_phone(),
            'organisation' => $this->faker->company,
        ]);

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/referrals?filter[service_id]={$service->id}&append=status_last_updated_at");

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
                'status_last_updated_at' => $referral->created_at->format(Carbon::ISO8601),
            ]
        ]);
    }

    /*
     * Create a referral.
     */

    public function test_guest_can_create_referral()
    {
        $service = factory(Service::class)->create([
            'referral_method' => Service::REFERRAL_METHOD_INTERNAL,
            'referral_email' => $this->faker->safeEmail,
        ]);

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
            'referee_phone' => random_uk_phone(),
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

    public function test_guest_can_create_self_referral()
    {
        $service = factory(Service::class)->create([
            'referral_method' => Service::REFERRAL_METHOD_INTERNAL,
            'referral_email' => $this->faker->safeEmail,
        ]);

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
        ];

        $response = $this->json('POST', '/core/v1/referrals', $payload);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment([
            'service_id' => $service->id,
            'name' => $payload['name'],
            'email' => $payload['email'],
            'phone' => null,
            'other_contact' => null,
            'postcode_outward_code' => null,
            'comments' => null,
            'referee_name' => null,
            'referee_email' => null,
            'referee_phone' => null,
            'referee_organisation' => null,
        ]);
    }

    public function test_audit_created_when_created()
    {
        $this->fakeEvents();

        $service = factory(Service::class)->create([
            'referral_method' => Service::REFERRAL_METHOD_INTERNAL,
            'referral_email' => $this->faker->safeEmail,
        ]);

        $response = $this->json('POST', '/core/v1/referrals', [
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
            'referee_phone' => random_uk_phone(),
            'organisation' => $this->faker->company,
        ]);

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($response) {
            return ($event->getAction() === Audit::ACTION_CREATE) &&
                ($event->getModel()->id === $this->getResponseContent($response)['data']['id']);
        });
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

    public function test_audit_created_when_viewed()
    {
        $this->fakeEvents();

        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);
        $referral = factory(Referral::class)->create([
            'service_id' => $service->id,
            'referral_consented_at' => $this->now,
        ]);

        Passport::actingAs($user);

        $this->json('GET', "/core/v1/referrals/{$referral->id}");

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($user, $referral) {
            return ($event->getAction() === Audit::ACTION_READ) &&
                ($event->getUser()->id === $user->id) &&
                ($event->getModel()->id === $referral->id);
        });
    }

    public function test_can_append_status_last_updated_at_when_viewed()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);
        $referral = factory(Referral::class)->create([
            'service_id' => $service->id,
            'referral_consented_at' => $this->now,
        ]);

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/referrals/{$referral->id}?append=status_last_updated_at");

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
                'status_last_updated_at' => $referral->created_at->format(Carbon::ISO8601),
            ]
        ]);
    }

    public function test_status_last_updated_at_uses_last_status_update_with_changed_status_when_viewed()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);
        $referral = factory(Referral::class)->create([
            'service_id' => $service->id,
            'referral_consented_at' => $this->now,
            'status' => Referral::STATUS_NEW,
        ]);
        $statusUpdate = $referral->statusUpdates()->create([
            'user_id' => $user->id,
            'from' => StatusUpdate::FROM_NEW,
            'to' => StatusUpdate::TO_IN_PROGRESS,
            'created_at' => now()->addDay(),
        ]);

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/referrals/{$referral->id}?append=status_last_updated_at");

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
                'status_last_updated_at' => $statusUpdate->created_at->format(Carbon::ISO8601),
            ]
        ]);
    }

    public function test_status_last_updated_at_uses_referral_created_at_with_unchanged_status_when_viewed()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);
        $referral = factory(Referral::class)->create([
            'service_id' => $service->id,
            'referral_consented_at' => $this->now,
            'status' => Referral::STATUS_NEW,
        ]);
        $statusUpdate = $referral->statusUpdates()->create([
            'user_id' => $user->id,
            'from' => StatusUpdate::FROM_NEW,
            'to' => StatusUpdate::TO_NEW,
            'created_at' => now()->addDay(),
        ]);

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/referrals/{$referral->id}?append=status_last_updated_at");

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
                'status_last_updated_at' => $referral->created_at->format(Carbon::ISO8601),
            ]
        ]);
    }

    /*
     * Update a specific referral.
     */

    public function test_guest_cannot_update_one()
    {
        $referral = factory(Referral::class)->create();

        $response = $this->json('PUT', "/core/v1/referrals/{$referral->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_for_another_service_cannot_update_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);
        $referral = factory(Referral::class)->create();

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/referrals/{$referral->id}", [
            'status' => Referral::STATUS_IN_PROGRESS,
            'comments' => 'Assigned to me',
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_worker_can_update_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);
        $referral = factory(Referral::class)->create([
            'service_id' => $service->id,
            'referral_consented_at' => $this->now,
        ]);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/referrals/{$referral->id}", [
            'status' => Referral::STATUS_IN_PROGRESS,
            'comments' => 'Assigned to me',
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $referral->id,
                'service_id' => $referral->service_id,
                'reference' => $referral->reference,
                'status' => Referral::STATUS_IN_PROGRESS,
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
            ]
        ]);
        $this->assertDatabaseHas((new StatusUpdate())->getTable(), [
            'user_id' => $user->id,
            'referral_id' => $referral->id,
            'from' => Referral::STATUS_NEW,
            'to' => Referral::STATUS_IN_PROGRESS,
            'comments' => 'Assigned to me',
        ]);
    }

    public function test_audit_created_when_updated()
    {
        $this->fakeEvents();

        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);
        $referral = factory(Referral::class)->create([
            'service_id' => $service->id,
            'referral_consented_at' => $this->now,
        ]);

        Passport::actingAs($user);

        $this->json('PUT', "/core/v1/referrals/{$referral->id}", [
            'status' => Referral::STATUS_IN_PROGRESS,
            'comments' => 'Assigned to me',
        ]);

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($user, $referral) {
            return ($event->getAction() === Audit::ACTION_UPDATE) &&
                ($event->getUser()->id === $user->id) &&
                ($event->getModel()->id === $referral->id);
        });
    }

    /*
     * Delete a specific referral.
     */

    public function test_guest_cannot_delete_one()
    {
        $referral = factory(Referral::class)->create();

        $response = $this->json('DELETE', "/core/v1/referrals/{$referral->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_delete_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);
        $referral = factory(Referral::class)->create();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/referrals/{$referral->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_admin_cannot_delete_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceAdmin($service);
        $referral = factory(Referral::class)->create();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/referrals/{$referral->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_organisation_admin_cannot_delete_one()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create();
        $user->makeOrganisationAdmin($organisation);
        $referral = factory(Referral::class)->create();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/referrals/{$referral->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_global_admin_cannot_delete_one()
    {
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $referral = factory(Referral::class)->create();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/referrals/{$referral->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_super_admin_cannot_delete_one()
    {
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();
        $referral = factory(Referral::class)->create();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/referrals/{$referral->id}");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing(table(Referral::class), ['id' => $referral->id]);
    }
}
