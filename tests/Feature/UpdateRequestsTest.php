<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Organisation;
use App\Models\Service;
use App\Models\ServiceLocation;
use App\Models\Taxonomy;
use App\Models\UpdateRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UpdateRequestsTest extends TestCase
{
    /*
     * List all the update requests.
     */

    public function test_guest_cannot_list_them()
    {
        $response = $this->json('GET', '/core/v1/update-requests');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_list_them()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);

        Passport::actingAs($user);
        $response = $this->json('GET', '/core/v1/update-requests');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_list_them()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);

        Passport::actingAs($user);
        $response = $this->json('GET', '/core/v1/update-requests');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_list_them()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);

        Passport::actingAs($user);
        $response = $this->json('GET', '/core/v1/update-requests');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_list_them()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();
        $location = factory(Location::class)->create();
        $updateRequest = $location->updateRequests()->create([
            'user_id' => $user->id,
            'data' => [
                'address_line_1' => $this->faker->streetAddress,
                'address_line_2' => null,
                'address_line_3' => null,
                'city' => $this->faker->city,
                'county' => 'West Yorkshire',
                'postcode' => $this->faker->postcode,
                'country' => 'United Kingdom',
                'accessibility_info' => null,
            ]
        ]);

        Passport::actingAs($user);
        $response = $this->json('GET', '/core/v1/update-requests');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $updateRequest->id,
            'user_id' => $user->id,
            'updateable_type' => 'locations',
            'updateable_id' => $location->id,
            'data' => [
                'address_line_1' => $updateRequest->data['address_line_1'],
                'address_line_2' => null,
                'address_line_3' => null,
                'city' => $updateRequest->data['city'],
                'county' => 'West Yorkshire',
                'postcode' => $updateRequest->data['postcode'],
                'country' => 'United Kingdom',
                'accessibility_info' => null,
            ],
        ]);
    }

    public function test_can_list_them_for_location()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();
        $location = factory(Location::class)->create();
        $locationUpdateRequest = $location->updateRequests()->create([
            'user_id' => $user->id,
            'data' => [
                'address_line_1' => $this->faker->streetAddress,
                'address_line_2' => null,
                'address_line_3' => null,
                'city' => $this->faker->city,
                'county' => 'West Yorkshire',
                'postcode' => $this->faker->postcode,
                'country' => 'United Kingdom',
                'accessibility_info' => null,
            ]
        ]);
        $organisation = factory(Organisation::class)->create();
        $organisationUpdateRequest = $organisation->updateRequests()->create([
            'user_id' => $user->id,
            'data' => [
                'name' => 'Test Name',
                'description' => 'Lorem ipsum',
                'url' => 'https://example.com',
                'email' => 'phpunit@example.com',
                'phone' => '07700000000',
            ],
        ]);

        Passport::actingAs($user);
        $response = $this->json('GET', "/core/v1/update-requests?filter[location_id]={$location->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $locationUpdateRequest->id]);
        $response->assertJsonMissing(['id' => $organisationUpdateRequest->id]);
    }

    /*
     * Get a specific update request.
     */

    public function test_guest_cannot_view_one()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => ['name' => 'Test Name'],
        ]);

        $response = $this->json('GET', "/core/v1/update-requests/{$updateRequest->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_view_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);
        Passport::actingAs($user);

        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => ['name' => 'Test Name'],
        ]);

        $response = $this->json('GET', "/core/v1/update-requests/{$updateRequest->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_view_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);
        Passport::actingAs($user);

        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => ['name' => 'Test Name'],
        ]);

        $response = $this->json('GET', "/core/v1/update-requests/{$updateRequest->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_view_one()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);
        Passport::actingAs($user);

        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => ['name' => 'Test Name'],
        ]);

        $response = $this->json('GET', "/core/v1/update-requests/{$updateRequest->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_view_one()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();
        Passport::actingAs($user);

        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => ['name' => 'Test Name'],
        ]);

        $response = $this->json('GET', "/core/v1/update-requests/{$updateRequest->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $updateRequest->id,
            'user_id' => $updateRequest->user_id,
            'updateable_type' => 'service_locations',
            'updateable_id' => $serviceLocation->id,
            'data' => ['name' => 'Test Name'],
        ]);
    }

    /*
     * Delete a specific update request.
     */

    public function test_guest_cannot_delete_one()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => ['name' => 'Test Name'],
        ]);

        $response = $this->json('DELETE', "/core/v1/update-requests/{$updateRequest->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_delete_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);
        Passport::actingAs($user);

        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => ['name' => 'Test Name'],
        ]);

        $response = $this->json('DELETE', "/core/v1/update-requests/{$updateRequest->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_delete_one()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);
        Passport::actingAs($user);

        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => ['name' => 'Test Name'],
        ]);

        $response = $this->json('DELETE', "/core/v1/update-requests/{$updateRequest->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_delete_one()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);
        Passport::actingAs($user);

        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => ['name' => 'Test Name'],
        ]);

        $response = $this->json('DELETE', "/core/v1/update-requests/{$updateRequest->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_delete_one()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();
        Passport::actingAs($user);

        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => ['name' => 'Test Name'],
        ]);

        $response = $this->json('DELETE', "/core/v1/update-requests/{$updateRequest->id}");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertSoftDeleted((new UpdateRequest())->getTable(), ['id' => $updateRequest->id]);
    }

    /*
     * Approve a specific update request.
     */

    public function test_guest_cannot_approve_one_for_service_location()
    {
        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => ['name' => 'Test Name'],
        ]);

        $response = $this->json('PUT', "/core/v1/update-requests/{$updateRequest->id}/approve");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_approve_one_for_service_location()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);
        Passport::actingAs($user);

        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => ['name' => 'Test Name'],
        ]);

        $response = $this->json('PUT', "/core/v1/update-requests/{$updateRequest->id}/approve");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_approve_one_for_service_location()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);
        Passport::actingAs($user);

        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => ['name' => 'Test Name'],
        ]);

        $response = $this->json('PUT', "/core/v1/update-requests/{$updateRequest->id}/approve");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_approve_one_for_service_location()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);
        Passport::actingAs($user);

        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => ['name' => 'Test Name'],
        ]);

        $response = $this->json('PUT', "/core/v1/update-requests/{$updateRequest->id}/approve");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_approve_one_for_service_location()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();
        Passport::actingAs($user);

        $serviceLocation = factory(ServiceLocation::class)->create();
        $updateRequest = $serviceLocation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => [
                'name' => 'Test Name',
                'regular_opening_hours' => [],
                'holiday_opening_hours' => [],
            ],
        ]);

        $response = $this->json('PUT', "/core/v1/update-requests/{$updateRequest->id}/approve");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing((new UpdateRequest())->getTable(), ['id' => $updateRequest->id, 'approved_at' => null]);
        $this->assertDatabaseHas((new ServiceLocation())->getTable(), ['id' => $serviceLocation->id, 'name' => 'Test Name']);
    }

    public function test_global_admin_can_approve_one_for_organisation()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();
        Passport::actingAs($user);

        $organisation = factory(Organisation::class)->create();
        $updateRequest = $organisation->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => [
                'name' => $this->faker->name,
                'description' => $this->faker->paragraph,
                'url' => $this->faker->url,
                'email' => $this->faker->safeEmail,
                'phone' => $this->faker->phoneNumber,
            ],
        ]);

        $response = $this->json('PUT', "/core/v1/update-requests/{$updateRequest->id}/approve");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing((new UpdateRequest())->getTable(), ['id' => $updateRequest->id, 'approved_at' => null]);
        $this->assertDatabaseHas((new Organisation())->getTable(), [
            'id' => $organisation->id,
            'name' => $updateRequest->data['name'],
            'description' => $updateRequest->data['description'],
            'url' => $updateRequest->data['url'],
            'email' => $updateRequest->data['email'],
            'phone' => $updateRequest->data['phone'],
        ]);
    }

    public function test_global_admin_can_approve_one_for_location()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();
        Passport::actingAs($user);

        $location = factory(Location::class)->create();
        $updateRequest = $location->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => [
                'address_line_1' => $this->faker->streetAddress,
                'address_line_2' => null,
                'address_line_3' => null,
                'city' => $this->faker->city,
                'county' => 'West Yorkshire',
                'postcode' => $this->faker->postcode,
                'country' => 'United Kingdom',
                'accessibility_info' => null,
            ],
        ]);

        $response = $this->json('PUT', "/core/v1/update-requests/{$updateRequest->id}/approve");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing((new UpdateRequest())->getTable(), ['id' => $updateRequest->id, 'approved_at' => null]);
        $this->assertDatabaseHas((new Location())->getTable(), [
            'id' => $location->id,
            'address_line_1' => $updateRequest->data['address_line_1'],
            'address_line_2' => $updateRequest->data['address_line_2'],
            'address_line_3' => $updateRequest->data['address_line_3'],
            'city' => $updateRequest->data['city'],
            'county' => $updateRequest->data['county'],
            'postcode' => $updateRequest->data['postcode'],
            'country' => $updateRequest->data['country'],
            'accessibility_info' => $updateRequest->data['accessibility_info'],
        ]);
    }

    public function test_global_admin_can_approve_one_for_service()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();
        Passport::actingAs($user);

        $service = factory(Service::class)->create();
        $service->serviceTaxonomies()->create([
            'taxonomy_id' => Taxonomy::category()->children()->firstOrFail()->id,
        ]);
        $updateRequest = $service->updateRequests()->create([
            'user_id' => factory(User::class)->create()->id,
            'data' => [
                'name' => 'Test Name',
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
                'useful_infos' => [],
                'social_medias' => [],
                'category_taxonomies' => $service->taxonomies()->pluck('taxonomies.id')->toArray(),
            ],
        ]);

        $response = $this->json('PUT', "/core/v1/update-requests/{$updateRequest->id}/approve");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing((new UpdateRequest())->getTable(), ['id' => $updateRequest->id, 'approved_at' => null]);
        $this->assertDatabaseHas((new Service())->getTable(), [
            'id' => $service->id,
            'name' => 'Test Name',
        ]);
    }
}
