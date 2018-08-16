<?php

namespace Tests\Feature;

use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\Notification;
use App\Models\Organisation;
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
        $notification = Notification::create([
            'user_id' => $user->id,
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
            [
                'id' => $notification->id,
                'user_id' => $notification->user_id,
                'channel' => $notification->channel,
                'recipient' => $notification->recipient,
                'message' => $notification->message,
                'created_at' => $notification->created_at->format(Carbon::ISO8601),
                'updated_at' => $notification->updated_at->format(Carbon::ISO8601),
            ]
        ]);
    }

    public function test_global_admin_can_list_them_for_specific_user()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $notification = Notification::create([
            'user_id' => $user->id,
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
        $response->assertJsonFragment([
            [
                'id' => $notification->id,
                'user_id' => $notification->user_id,
                'channel' => $notification->channel,
                'recipient' => $notification->recipient,
                'message' => $notification->message,
                'created_at' => $notification->created_at->format(Carbon::ISO8601),
                'updated_at' => $notification->updated_at->format(Carbon::ISO8601),
            ]
        ]);
        $response->assertJsonMissing([
            [
                'id' => $anotherNotification->id,
                'user_id' => $anotherNotification->user_id,
                'channel' => $anotherNotification->channel,
                'recipient' => $anotherNotification->recipient,
                'message' => $anotherNotification->message,
                'created_at' => $anotherNotification->created_at->format(Carbon::ISO8601),
                'updated_at' => $anotherNotification->updated_at->format(Carbon::ISO8601),
            ]
        ]);
    }

    public function test_audit_created_when_listed()
    {
        $this->fakeEvents();

        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $notification = Notification::create([
            'user_id' => $user->id,
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
            'user_id' => $notification->user_id,
            'channel' => $notification->channel,
            'recipient' => $notification->recipient,
            'message' => $notification->message,
            'created_at' => $notification->created_at->format(Carbon::ISO8601),
            'updated_at' => $notification->updated_at->format(Carbon::ISO8601),
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
