<?php

namespace Tests\Feature;

use App\Models\Organisation;
use App\Models\PageFeedback;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PageFeedbacksTest extends TestCase
{
    /*
     * List all the page feedbacks.
     */

    public function test_guest_cannot_list_them()
    {
        $response = $this->json('GET', '/core/v1/page-feedbacks');

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

        $response = $this->json('GET', '/core/v1/page-feedbacks');

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

        $response = $this->json('GET', '/core/v1/page-feedbacks');

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

        $response = $this->json('GET', '/core/v1/page-feedbacks');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_list_them()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();
        $pageFeedback = PageFeedback::create([
            'url' => url('/test'),
            'feedback' => 'This page does not work',
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/page-feedbacks');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            [
                'id' => $pageFeedback->id,
                'url' => url('/test'),
                'feedback' => 'This page does not work',
                'created_at' => $pageFeedback->created_at->format(Carbon::ISO8601),
                'updated_at' => $pageFeedback->updated_at->format(Carbon::ISO8601),
            ]
        ]);
    }

    /*
     * Create a page feedback.
     */

    public function test_guest_can_create_one()
    {
        $payload = [
            'url' => url('test-page'),
            'feedback' => 'This page does not work',
        ];

        $response = $this->json('POST', '/core/v1/page-feedbacks', $payload);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment($payload);
    }
}
