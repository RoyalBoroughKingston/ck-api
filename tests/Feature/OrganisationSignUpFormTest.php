<?php

namespace Tests\Feature;

use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\Organisation;
use App\Models\Service;
use App\Models\SocialMedia;
use App\Models\UpdateRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Passport;
use Tests\TestCase;

class OrganisationSignUpFormTest extends TestCase
{
    // Store.

    public function test_guest_can_create_one()
    {
        $response = $this->json('POST', '/core/v1/organisation-sign-up-forms', [
            'user' => [
                'first_name' => $this->faker->firstName,
                'last_name' => $this->faker->lastName,
                'email' => $this->faker->safeEmail,
                'phone' => random_uk_phone(),
            ],
            'organisation' => [
                'slug' => 'test-org',
                'name' => 'Test Org',
                'description' => 'Test description',
                'url' => 'http://test-org.example.com',
                'email' => 'info@test-org.example.com',
                'phone' => '07700000000',
            ],
            'service' => [
                'slug' => 'test-service',
                'name' => 'Test Service',
                'type' => Service::TYPE_SERVICE,
                'intro' => 'This is a test intro',
                'description' => 'Lorem ipsum',
                'wait_time' => null,
                'is_free' => true,
                'fees_text' => null,
                'fees_url' => null,
                'testimonial' => null,
                'video_embed' => null,
                'url' => $this->faker->url,
                'contact_name' => $this->faker->name,
                'contact_phone' => random_uk_phone(),
                'contact_email' => $this->faker->safeEmail,
                'criteria' => [
                    'age_group' => '18+',
                    'disability' => null,
                    'employment' => null,
                    'gender' => null,
                    'housing' => null,
                    'income' => null,
                    'language' => null,
                    'other' => null,
                ],
                'useful_infos' => [
                    [
                        'title' => 'Did you know?',
                        'description' => 'Lorem ipsum',
                        'order' => 1,
                    ],
                ],
                'offerings' => [
                    [
                        'offering' => 'Weekly club',
                        'order' => 1,
                    ],
                ],
                'social_medias' => [
                    [
                        'type' => SocialMedia::TYPE_INSTAGRAM,
                        'url' => 'https://www.instagram.com/ayupdigital',
                    ],
                ],
            ],
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function test_guest_can_create_one_with_single_form_of_contact()
    {
        $response = $this->json('POST', '/core/v1/organisation-sign-up-forms', [
            'user' => [
                'first_name' => $this->faker->firstName,
                'last_name' => $this->faker->lastName,
                'email' => $this->faker->safeEmail,
                'phone' => random_uk_phone(),
            ],
            'organisation' => [
                'slug' => 'test-org',
                'name' => 'Test Org',
                'description' => 'Test description',
                'url' => 'http://test-org.example.com',
                'email' => 'info@test-org.example.com',
                'phone' => null,
            ],
            'service' => [
                'slug' => 'test-service',
                'name' => 'Test Service',
                'type' => Service::TYPE_SERVICE,
                'intro' => 'This is a test intro',
                'description' => 'Lorem ipsum',
                'wait_time' => null,
                'is_free' => true,
                'fees_text' => null,
                'fees_url' => null,
                'testimonial' => null,
                'video_embed' => null,
                'url' => $this->faker->url,
                'contact_name' => $this->faker->name,
                'contact_phone' => random_uk_phone(),
                'contact_email' => null,
                'criteria' => [
                    'age_group' => '18+',
                    'disability' => null,
                    'employment' => null,
                    'gender' => null,
                    'housing' => null,
                    'income' => null,
                    'language' => null,
                    'other' => null,
                ],
                'useful_infos' => [
                    [
                        'title' => 'Did you know?',
                        'description' => 'Lorem ipsum',
                        'order' => 1,
                    ],
                ],
                'offerings' => [
                    [
                        'offering' => 'Weekly club',
                        'order' => 1,
                    ],
                ],
                'social_medias' => [
                    [
                        'type' => SocialMedia::TYPE_INSTAGRAM,
                        'url' => 'https://www.instagram.com/ayupdigital',
                    ],
                ],
            ],
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function test_service_worker_cannot_create_one()
    {
        /** @var \App\Models\Service $service */
        $service = factory(Service::class)->create();
        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/organisation-sign-up-forms');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_create_one()
    {
        /** @var \App\Models\Service $service */
        $service = factory(Service::class)->create();
        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();
        $user->makeServiceAdmin($service);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/organisation-sign-up-forms');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_create_one()
    {
        /** @var \App\Models\Organisation $organisation */
        $organisation = factory(Organisation::class)->create();
        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();
        $user->makeOrganisationAdmin($organisation);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/organisation-sign-up-forms');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_cannot_create_one()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeGlobalAdmin();

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/organisation-sign-up-forms');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_super_admin_cannot_create_one()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = factory(User::class)->create();
        $user->makeSuperAdmin();

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/organisation-sign-up-forms');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_audit_created_when_created()
    {
        $this->fakeEvents();

        $response = $this->json('POST', '/core/v1/organisation-sign-up-forms', [
            'user' => [
                'first_name' => $this->faker->firstName,
                'last_name' => $this->faker->lastName,
                'email' => $this->faker->safeEmail,
                'phone' => random_uk_phone(),
            ],
            'organisation' => [
                'slug' => 'test-org',
                'name' => 'Test Org',
                'description' => 'Test description',
                'url' => 'http://test-org.example.com',
                'email' => 'info@test-org.example.com',
                'phone' => '07700000000',
            ],
            'service' => [
                'slug' => 'test-service',
                'name' => 'Test Service',
                'type' => Service::TYPE_SERVICE,
                'intro' => 'This is a test intro',
                'description' => 'Lorem ipsum',
                'wait_time' => null,
                'is_free' => true,
                'fees_text' => null,
                'fees_url' => null,
                'testimonial' => null,
                'video_embed' => null,
                'url' => $this->faker->url,
                'contact_name' => $this->faker->name,
                'contact_phone' => random_uk_phone(),
                'contact_email' => $this->faker->safeEmail,
                'criteria' => [
                    'age_group' => '18+',
                    'disability' => null,
                    'employment' => null,
                    'gender' => null,
                    'housing' => null,
                    'income' => null,
                    'language' => null,
                    'other' => null,
                ],
                'useful_infos' => [
                    [
                        'title' => 'Did you know?',
                        'description' => 'Lorem ipsum',
                        'order' => 1,
                    ],
                ],
                'offerings' => [
                    [
                        'offering' => 'Weekly club',
                        'order' => 1,
                    ],
                ],
                'social_medias' => [
                    [
                        'type' => SocialMedia::TYPE_INSTAGRAM,
                        'url' => 'https://www.instagram.com/ayupdigital',
                    ],
                ],
            ],
        ]);

        Event::assertDispatched(EndpointHit::class, function (EndpointHit $event) use ($response) {
            /** @var \App\Models\UpdateRequest $updateRequest */
            $updateRequest = UpdateRequest::findOrFail(
                $this->getResponseContent($response, 'id')
            );

            return ($event->getAction() === Audit::ACTION_CREATE) &&
                ($event->getUser() === null) &&
                ($event->getModel()->is($updateRequest));
        });
    }
}
