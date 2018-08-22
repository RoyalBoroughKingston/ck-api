<?php

namespace Tests\Feature;

use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\Notification;
use App\Models\Organisation;
use App\Models\Referral;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Passport;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    /*
     * List all the notifications.
     */

    public function test_guest_cannot_list_them()
    {
        $response = $this->json('GET', '/core/v1/notifications');

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

        $response = $this->json('GET', '/core/v1/notifications');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_list_them()
    {
        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceAdmin($service);

        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/notifications');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_list_them()
    {
        /**
         * @var \App\Models\Organisation $organisation
         * @var \App\Models\User $user
         */
        $service = factory(Organisation::class)->create();
        $user = factory(User::class)->create();
        $user->makeOrganisationAdmin($service);

        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/notifications');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_list_them()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $notification = $user->notifications()->create([
            'channel' => Notification::CHANNEL_EMAIL,
            'recipient' => 'test@example.com',
            'message' => 'This is a test',
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/notifications');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $notification->id,
            'notifiable_type' => 'users',
            'notifiable_id' => $user->id,
            'channel' => $notification->channel,
            'recipient' => $notification->recipient,
            'message' => $notification->message,
            'created_at' => $notification->created_at->format(Carbon::ISO8601),
            'updated_at' => $notification->updated_at->format(Carbon::ISO8601),
        ]);
    }

    public function test_global_admin_can_list_them_for_specific_user()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $notification = $user->notifications()->create([
            'channel' => Notification::CHANNEL_EMAIL,
            'recipient' => 'test@example.com',
            'message' => 'This is a test',
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);
        $anotherNotification = Notification::create([
            'channel' => Notification::CHANNEL_SMS,
            'recipient' => '07700000000',
            'message' => 'Another notification',
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/notifications?filter[user_id]={$user->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $notification->id]);
        $response->assertJsonMissing(['id' => $anotherNotification->id]);
    }

    public function test_global_admin_can_list_them_for_referral()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $referral = factory(Referral::class)->create();
        $notification = $referral->notifications()->create([
            'channel' => Notification::CHANNEL_EMAIL,
            'recipient' => 'test@example.com',
            'message' => 'This is a test',
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);
        $anotherNotification = Notification::create([
            'channel' => Notification::CHANNEL_SMS,
            'recipient' => '07700000000',
            'message' => 'Another notification',
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/notifications?filter[referral_id]={$referral->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $notification->id]);
        $response->assertJsonMissing(['id' => $anotherNotification->id]);
    }

    public function test_global_admin_can_list_them_for_service()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $service = factory(Service::class)->create();
        $notification = $service->notifications()->create([
            'channel' => Notification::CHANNEL_EMAIL,
            'recipient' => 'test@example.com',
            'message' => 'This is a test',
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);
        $anotherNotification = Notification::create([
            'channel' => Notification::CHANNEL_SMS,
            'recipient' => '07700000000',
            'message' => 'Another notification',
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/notifications?filter[service_id]={$service->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $notification->id]);
        $response->assertJsonMissing(['id' => $anotherNotification->id]);
    }

    public function test_audit_created_when_listed()
    {
        $this->fakeEvents();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $notification = $user->notifications()->create([
            'channel' => Notification::CHANNEL_EMAIL,
            'recipient' => 'test@example.com',
            'message' => 'This is a test',
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        Passport::actingAs($user);

        $this->json('GET', '/core/v1/notifications');

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($user, $notification) {
            return ($event->getAction() === Audit::ACTION_READ) &&
                ($event->getUser()->id === $user->id);
        });
    }

    /*
     * Get a specific notification.
     */

    public function test_guest_cannot_view_one()
    {
        $notification = Notification::create([
            'channel' => Notification::CHANNEL_EMAIL,
            'recipient' => 'test@example.com',
            'message' => 'This is a test',
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        $response = $this->json('GET', "/core/v1/notifications/{$notification->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_view_one()
    {
        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);
        $notification = Notification::create([
            'channel' => Notification::CHANNEL_EMAIL,
            'recipient' => 'test@example.com',
            'message' => 'This is a test',
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/notifications/{$notification->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_view_one()
    {
        /**
         * @var \App\Models\Service $service
         * @var \App\Models\User $user
         */
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceAdmin($service);
        $notification = Notification::create([
            'channel' => Notification::CHANNEL_EMAIL,
            'recipient' => 'test@example.com',
            'message' => 'This is a test',
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/notifications/{$notification->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_view_one()
    {
        /**
         * @var \App\Models\Organisation $organisation
         * @var \App\Models\User $user
         */
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create();
        $user->makeOrganisationAdmin($organisation);
        $notification = Notification::create([
            'channel' => Notification::CHANNEL_EMAIL,
            'recipient' => 'test@example.com',
            'message' => 'This is a test',
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/notifications/{$notification->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_view_one()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $notification = Notification::create([
            'channel' => Notification::CHANNEL_EMAIL,
            'recipient' => 'test@example.com',
            'message' => 'This is a test',
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        Passport::actingAs($user);

        $response = $this->json('GET', "/core/v1/notifications/{$notification->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $notification->id,
            'notifiable_type' => null,
            'notifiable_id' => null,
            'channel' => Notification::CHANNEL_EMAIL,
            'recipient' => 'test@example.com',
            'message' => 'This is a test',
            'created_at' => $this->now->format(Carbon::ISO8601),
            'updated_at' => $this->now->format(Carbon::ISO8601),
        ]);
    }

    public function test_audit_created_when_viewed()
    {
        $this->fakeEvents();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $notification = Notification::create([
            'channel' => Notification::CHANNEL_EMAIL,
            'recipient' => 'test@example.com',
            'message' => 'This is a test',
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        Passport::actingAs($user);

        $this->json('GET', "/core/v1/notifications/{$notification->id}");

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($user, $notification) {
            return ($event->getAction() === Audit::ACTION_READ) &&
                ($event->getUser()->id === $user->id) &&
                ($event->getModel()->id === $notification->id);
        });
    }
}
