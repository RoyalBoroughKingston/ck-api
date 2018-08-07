<?php

namespace Tests\Feature;

use App\Models\Organisation;
use App\Models\Service;
use App\Models\SocialMedia;
use App\Models\Taxonomy;
use App\Models\UpdateRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ServicesTest extends TestCase
{
    /*
     * List all the services.
     */

    public function test_guest_can_list_them()
    {
        $service = factory(Service::class)->create();
        $service->usefulInfos()->create([
            'title' => 'Did You Know?',
            'description' => 'This is a test description',
            'order' => 1,
        ]);
        $service->socialMedias()->create([
            'type' => SocialMedia::TYPE_INSTAGRAM,
            'url' => 'https://www.instagram.com/ayupdigital/'
        ]);
        $service->serviceTaxonomies()->create([
            'taxonomy_id' => Taxonomy::category()->children()->first()->id,
        ]);

        $response = $this->json('GET', '/core/v1/services');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $service->id,
            'organisation_id' => $service->organisation_id,
            'name' => $service->name,
            'status' => $service->status,
            'intro' => $service->intro,
            'description' => $service->description,
            'wait_time' => $service->wait_time,
            'is_free' => $service->is_free,
            'fees_text' => $service->fees_text,
            'fees_url' => $service->fees_url,
            'testimonial' => $service->testimonial,
            'video_embed' => $service->video_embed,
            'url' => $service->url,
            'contact_name' => $service->contact_name,
            'contact_phone' => $service->contact_phone,
            'contact_email' => $service->contact_email,
            'show_referral_disclaimer' => $service->show_referral_disclaimer,
            'referral_method' => $service->referral_method,
            'referral_button_text' => $service->referral_button_text,
            'referral_email' => $service->referral_email,
            'referral_url' => $service->referral_url,
            'criteria' => [
                'age_group' => $service->serviceCriterion->age_group,
                'disability' => $service->serviceCriterion->disability,
                'employment' => $service->serviceCriterion->employment,
                'gender' => $service->serviceCriterion->gender,
                'housing' => $service->serviceCriterion->housing,
                'income' => $service->serviceCriterion->income,
                'language' => $service->serviceCriterion->language,
                'other' => $service->serviceCriterion->other,
            ],
            'seo_title' => $service->seo_title,
            'seo_description' => $service->seo_description,
            'useful_infos' => [
                [
                    'title' => 'Did You Know?',
                    'description' => 'This is a test description',
                    'order' => 1,
                ]
            ],
            'social_medias' => [
                [
                    'type' => SocialMedia::TYPE_INSTAGRAM,
                    'url' => 'https://www.instagram.com/ayupdigital/'
                ]
            ],
            'category_taxonomies' => [
                [
                    'id' => Taxonomy::category()->children()->first()->id,
                    'parent_id' => Taxonomy::category()->children()->first()->parent_id,
                    'name' => Taxonomy::category()->children()->first()->name,
                    'created_at' => Taxonomy::category()->children()->first()->created_at->format(Carbon::ISO8601),
                    'updated_at' => Taxonomy::category()->children()->first()->updated_at->format(Carbon::ISO8601),
                ]
            ],
            'created_at' => $service->created_at->format(Carbon::ISO8601)
        ]);
    }

    public function test_guest_can_list_them_for_organisation()
    {
        $anotherService = factory(Service::class)->create();
        $service = factory(Service::class)->create();
        $service->usefulInfos()->create([
            'title' => 'Did You Know?',
            'description' => 'This is a test description',
            'order' => 1,
        ]);
        $service->socialMedias()->create([
            'type' => SocialMedia::TYPE_INSTAGRAM,
            'url' => 'https://www.instagram.com/ayupdigital/'
        ]);
        $service->serviceTaxonomies()->create([
            'taxonomy_id' => Taxonomy::category()->children()->first()->id,
        ]);

        $response = $this->json('GET', "/core/v1/services?filter[organisation_id]={$service->organisation_id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $service->id,
            'organisation_id' => $service->organisation_id,
            'name' => $service->name,
            'status' => $service->status,
            'intro' => $service->intro,
            'description' => $service->description,
            'wait_time' => $service->wait_time,
            'is_free' => $service->is_free,
            'fees_text' => $service->fees_text,
            'fees_url' => $service->fees_url,
            'testimonial' => $service->testimonial,
            'video_embed' => $service->video_embed,
            'url' => $service->url,
            'contact_name' => $service->contact_name,
            'contact_phone' => $service->contact_phone,
            'contact_email' => $service->contact_email,
            'show_referral_disclaimer' => $service->show_referral_disclaimer,
            'referral_method' => $service->referral_method,
            'referral_button_text' => $service->referral_button_text,
            'referral_email' => $service->referral_email,
            'referral_url' => $service->referral_url,
            'criteria' => [
                'age_group' => $service->serviceCriterion->age_group,
                'disability' => $service->serviceCriterion->disability,
                'employment' => $service->serviceCriterion->employment,
                'gender' => $service->serviceCriterion->gender,
                'housing' => $service->serviceCriterion->housing,
                'income' => $service->serviceCriterion->income,
                'language' => $service->serviceCriterion->language,
                'other' => $service->serviceCriterion->other,
            ],
            'seo_title' => $service->seo_title,
            'seo_description' => $service->seo_description,
            'useful_infos' => [
                [
                    'title' => 'Did You Know?',
                    'description' => 'This is a test description',
                    'order' => 1,
                ]
            ],
            'social_medias' => [
                [
                    'type' => SocialMedia::TYPE_INSTAGRAM,
                    'url' => 'https://www.instagram.com/ayupdigital/'
                ]
            ],
            'category_taxonomies' => [
                [
                    'id' => Taxonomy::category()->children()->first()->id,
                    'parent_id' => Taxonomy::category()->children()->first()->parent_id,
                    'name' => Taxonomy::category()->children()->first()->name,
                    'created_at' => Taxonomy::category()->children()->first()->created_at->format(Carbon::ISO8601),
                    'updated_at' => Taxonomy::category()->children()->first()->updated_at->format(Carbon::ISO8601),
                ]
            ],
            'created_at' => $service->created_at->format(Carbon::ISO8601)
        ]);
        $response->assertJsonMissing([
            'id' => $anotherService->id,
        ]);
    }

    /*
     * Create a service.
     */

    public function test_guest_cannot_create_one()
    {
        $response = $this->json('POST', '/core/v1/services');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_create_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/services');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_create_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);

        Passport::actingAs($user);

        $response = $this->json('POST', '/core/v1/services');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_can_create_one()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);

        Passport::actingAs($user);

        $payload = [
            'organisation_id' => $organisation->id,
            'name' => 'Test Service',
            'status' => Service::STATUS_ACTIVE,
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
            'contact_phone' => $this->faker->phoneNumber,
            'contact_email' => $this->faker->safeEmail,
            'show_referral_disclaimer' => true,
            'referral_method' => Service::REFERRAL_METHOD_INTERNAL,
            'referral_button_text' => null,
            'referral_email' => null,
            'referral_url' => null,
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
            'seo_title' => 'This is a SEO title',
            'seo_description' => 'This is a SEO description',
            'useful_infos' => [
                [
                    'title' => 'Did you know?',
                    'description' => 'Lorem ipsum',
                    'order' => 1,
                ]
            ],
            'social_medias' => [
                [
                    'type' => SocialMedia::TYPE_INSTAGRAM,
                    'url' => 'https://www.instagram.com/ayupdigital',
                ]
            ],
            'category_taxonomies' => [Taxonomy::category()->firstOrFail()->id],
        ];
        $response = $this->json('POST', '/core/v1/services', $payload);

        $response->assertStatus(Response::HTTP_CREATED);
        $responsePayload = $payload;
        $responsePayload['category_taxonomies'] = [
            [
                'id' => Taxonomy::category()->firstOrFail()->id,
                'parent_id' => Taxonomy::category()->firstOrFail()->parent_id,
                'name' => Taxonomy::category()->firstOrFail()->name,
                'created_at' => Taxonomy::category()->firstOrFail()->created_at->format(Carbon::ISO8601),
                'updated_at' => Taxonomy::category()->firstOrFail()->updated_at->format(Carbon::ISO8601),
            ]
        ];
        $response->assertJsonFragment($responsePayload);
    }

    /*
     * Get a specific service.
     */

    public function test_guest_can_get_one()
    {
        $service = factory(Service::class)->create();
        $service->usefulInfos()->create([
            'title' => 'Did You Know?',
            'description' => 'This is a test description',
            'order' => 1,
        ]);
        $service->socialMedias()->create([
            'type' => SocialMedia::TYPE_INSTAGRAM,
            'url' => 'https://www.instagram.com/ayupdigital/'
        ]);
        $service->serviceTaxonomies()->create([
            'taxonomy_id' => Taxonomy::category()->children()->first()->id,
        ]);

        $response = $this->json('GET', "/core/v1/services/{$service->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $service->id,
            'organisation_id' => $service->organisation_id,
            'name' => $service->name,
            'status' => $service->status,
            'intro' => $service->intro,
            'description' => $service->description,
            'wait_time' => $service->wait_time,
            'is_free' => $service->is_free,
            'fees_text' => $service->fees_text,
            'fees_url' => $service->fees_url,
            'testimonial' => $service->testimonial,
            'video_embed' => $service->video_embed,
            'url' => $service->url,
            'contact_name' => $service->contact_name,
            'contact_phone' => $service->contact_phone,
            'contact_email' => $service->contact_email,
            'show_referral_disclaimer' => $service->show_referral_disclaimer,
            'referral_method' => $service->referral_method,
            'referral_button_text' => $service->referral_button_text,
            'referral_email' => $service->referral_email,
            'referral_url' => $service->referral_url,
            'criteria' => [
                'age_group' => $service->serviceCriterion->age_group,
                'disability' => $service->serviceCriterion->disability,
                'employment' => $service->serviceCriterion->employment,
                'gender' => $service->serviceCriterion->gender,
                'housing' => $service->serviceCriterion->housing,
                'income' => $service->serviceCriterion->income,
                'language' => $service->serviceCriterion->language,
                'other' => $service->serviceCriterion->other,
            ],
            'seo_title' => $service->seo_title,
            'seo_description' => $service->seo_description,
            'useful_infos' => [
                [
                    'title' => 'Did You Know?',
                    'description' => 'This is a test description',
                    'order' => 1,
                ]
            ],
            'social_medias' => [
                [
                    'type' => SocialMedia::TYPE_INSTAGRAM,
                    'url' => 'https://www.instagram.com/ayupdigital/'
                ]
            ],
            'category_taxonomies' => [
                [
                    'id' => Taxonomy::category()->children()->first()->id,
                    'parent_id' => Taxonomy::category()->children()->first()->parent_id,
                    'name' => Taxonomy::category()->children()->first()->name,
                    'created_at' => Taxonomy::category()->children()->first()->created_at->format(Carbon::ISO8601),
                    'updated_at' => Taxonomy::category()->children()->first()->updated_at->format(Carbon::ISO8601),
                ]
            ],
            'created_at' => $service->created_at->format(Carbon::ISO8601)
        ]);
    }

    /*
     * Update a specific service.
     */

    public function test_guest_cannot_update_one()
    {
        $service = factory(Service::class)->create();

        $response = $this->json('PUT', "/core/v1/services/{$service->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_update_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);

        Passport::actingAs($user);

        $response = $this->json('PUT', "/core/v1/services/{$service->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_can_update_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);

        Passport::actingAs($user);

        $payload = [
            'name' => 'Test Service',
            'status' => Service::STATUS_ACTIVE,
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
            'contact_phone' => $this->faker->phoneNumber,
            'contact_email' => $this->faker->safeEmail,
            'show_referral_disclaimer' => true,
            'referral_method' => Service::REFERRAL_METHOD_INTERNAL,
            'referral_button_text' => null,
            'referral_email' => null,
            'referral_url' => null,
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
            'seo_title' => 'This is a SEO title',
            'seo_description' => 'This is a SEO description',
            'useful_infos' => [
                [
                    'title' => 'Did you know?',
                    'description' => 'Lorem ipsum',
                    'order' => 1,
                ]
            ],
            'social_medias' => [
                [
                    'type' => SocialMedia::TYPE_INSTAGRAM,
                    'url' => 'https://www.instagram.com/ayupdigital',
                ]
            ],
            'category_taxonomies' => [Taxonomy::category()->firstOrFail()->id],
        ];
        $response = $this->json('PUT', "/core/v1/services/{$service->id}", $payload);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['data' => $payload]);
    }

    /*
     * Delete a specific service.
     */

    public function test_guest_cannot_delete_one()
    {
        $service = factory(Service::class)->create();

        $response = $this->json('DELETE', "/core/v1/services/{$service->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_delete_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/services/{$service->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_delete_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/services/{$service->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_delete_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($service->organisation);

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/services/{$service->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_cannot_delete_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeGlobalAdmin();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/services/{$service->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_super_admin_can_delete_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeSuperAdmin();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/core/v1/services/{$service->id}");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing((new Service())->getTable(), ['id' => $service->id]);
    }

    /*
     * Get a specific service's logo.
     */

    public function test_guest_can_view_logo()
    {
        $service = factory(Service::class)->create();

        $response = $this->get("/core/v1/services/{$service->id}/logo");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertHeader('Content-Type', 'image/png');
    }

    /*
     * Upload a specific service's logo.
     */

    public function test_guest_cannot_upload_logo()
    {
        $service = factory(Service::class)->create();

        $response = $this->json('POST', "/core/v1/services/{$service->id}/logo");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_upload_logo()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceWorker($service);

        Passport::actingAs($user);

        $response = $this->json('POST', "/core/v1/services/{$service->id}/logo");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_can_upload_logo()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create();
        $user->makeServiceAdmin($service);
        $image = Storage::disk('local')->get('/test-data/image.png');

        Passport::actingAs($user);

        $response = $this->json('POST', "/core/v1/services/{$service->id}/logo", [
            'file' => 'data:image/png;base64,' . base64_encode($image),
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['message' => 'The update request has been received and needs to be reviewed']);
        $this->assertDatabaseHas((new UpdateRequest())->getTable(), [
            'user_id' => $user->id,
            'updateable_type' => 'services',
            'updateable_id' => $service->id,
        ]);
    }

    /*
     * Delete a specific service's logo.
     */

    /*
     * Get a specific service's SEO image.
     */

    /*
     * Upload a specific service's SEO image.
     */

    /*
     * Delete a specific service's SEO image.
     */
}
