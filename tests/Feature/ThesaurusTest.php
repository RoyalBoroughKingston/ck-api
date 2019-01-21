<?php

namespace Tests\Feature;

use App\Models\Organisation;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ThesaurusTest extends TestCase
{
    /*
     * View the thesaurus.
     */

    public function test_guest_cannot_view_thesaurus()
    {
        $response = $this->json('GET', '/core/v1/thesaurus');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_view_thesaurus()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);

        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/thesaurus');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_view_thesaurus()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);

        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/thesaurus');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_view_thesaurus()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);

        Passport::actingAs($user);

        $response = $this->json('GET', '/core/v1/thesaurus');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_view_thesaurus()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();

        Passport::actingAs($user);
        $response = $this->json('GET', '/core/v1/thesaurus');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                ['autism', 'autistic', 'asd'],
                ['not drinking', 'dehydration'],
                ['dehydration', 'thirsty', 'drought'],
            ],
        ]);
    }

    /*
     * Update the thesaurus.
     */

    public function test_guest_cannot_update_thesaurus()
    {
        $response = $this->json('PUT', '/core/v1/thesaurus');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_service_worker_cannot_update_thesaurus()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceWorker($service);

        Passport::actingAs($user);

        $response = $this->json('PUT', '/core/v1/thesaurus');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_service_admin_cannot_update_thesaurus()
    {
        $service = factory(Service::class)->create();
        $user = factory(User::class)->create()->makeServiceAdmin($service);

        Passport::actingAs($user);

        $response = $this->json('PUT', '/core/v1/thesaurus');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_organisation_admin_cannot_update_thesaurus()
    {
        $organisation = factory(Organisation::class)->create();
        $user = factory(User::class)->create()->makeOrganisationAdmin($organisation);

        Passport::actingAs($user);

        $response = $this->json('PUT', '/core/v1/thesaurus');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_global_admin_can_update_thesaurus()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();

        Passport::actingAs($user);
        $response = $this->json('PUT', '/core/v1/thesaurus', [
            'synonyms' => [
                ['persons', 'people'],
            ],
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                ['persons', 'people'],
            ]
        ]);
    }

    public function test_thesaurus_works_with_search()
    {
        $service = factory(Service::class)->create([
            'name' => 'Helping People',
        ]);
        $user = factory(User::class)->create()->makeGlobalAdmin();

        Passport::actingAs($user);
        $updateResponse = $this->json('PUT', '/core/v1/thesaurus', [
            'synonyms' => [
                ['persons', 'people'],
            ],
        ]);

        $updateResponse->assertStatus(Response::HTTP_OK);

        $searchResponse = $this->json('POST', '/core/v1/search', [
            'query' => 'persons',
        ]);
        $searchResponse->assertJsonFragment([
            'id' => $service->id,
        ]);
    }

    /*
     * Multi-word synonyms.
     */

    public function test_invalid_multi_word_upload_fails()
    {
        $user = factory(User::class)->create()->makeGlobalAdmin();

        Passport::actingAs($user);
        $response = $this->json('PUT', '/core/v1/thesaurus', [
            'synonyms' => [
                ['multi word', 'another here'],
            ],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_simple_contraction_works_with_search()
    {
        $service = factory(Service::class)->create([
            'name' => 'People Not Drinking Enough',
        ]);
        $user = factory(User::class)->create()->makeGlobalAdmin();

        Passport::actingAs($user);
        $updateResponse = $this->json('PUT', '/core/v1/thesaurus', [
            'synonyms' => [
                ['not drinking', 'dehydration'],
            ],
        ]);

        $updateResponse->assertStatus(Response::HTTP_OK);

        // Using single word.
        $searchResponse = $this->json('POST', '/core/v1/search', [
            'query' => 'dehydration',
        ]);
        $searchResponse->assertJsonFragment([
            'id' => $service->id,
        ]);

        // Using multi-word.
        $searchResponse = $this->json('POST', '/core/v1/search', [
            'query' => 'not drinking',
        ]);
        $searchResponse->assertJsonFragment([
            'id' => $service->id,
        ]);
    }

    public function test_simple_contraction_works_with_further_synonyms_with_search()
    {
        $this->markTestIncomplete();
    }
}
