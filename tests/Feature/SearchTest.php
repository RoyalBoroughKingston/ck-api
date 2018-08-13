<?php

namespace Tests\Feature;

use App\Models\Organisation;
use App\Models\Service;
use App\Models\Taxonomy;
use Illuminate\Http\Response;
use Tests\TestCase;

class SearchTest extends TestCase
{
    /*
     * Perform a search for services.
     */

    public function test_guest_can_search()
    {
        $response = $this->json('POST', '/core/v1/search', [
            'query' => 'test',
        ]);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_query_matches_service_name()
    {
        $service = factory(Service::class)->create();

        $response = $this->json('POST', '/core/v1/search', [
            'query' => $service->name,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $service->id,
        ]);
    }

    public function test_query_matches_service_description()
    {
        $service = factory(Service::class)->create();

        $response = $this->json('POST', '/core/v1/search', [
            'query' => $service->description,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $service->id,
        ]);
    }

    public function test_query_matches_taxonomy_name()
    {
        $service = factory(Service::class)->create();
        $taxonomy = Taxonomy::category()->children()->firstOrFail();
        $service->serviceTaxonomies()->create(['taxonomy_id' => $taxonomy->id]);

        $response = $this->json('POST', '/core/v1/search', [
            'query' => $taxonomy->name,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $service->id]);
    }

    public function test_query_matches_organisation_name()
    {
        $service = factory(Service::class)->create();

        $response = $this->json('POST', '/core/v1/search', [
            'query' => $service->organisation->name,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $service->id,
        ]);
    }

    public function test_query_ranks_service_name_above_organisation_name()
    {
        $organisation = factory(Organisation::class)->create(['name' => 'Test Name']);
        $serviceWithRelevantOrganisationName = factory(Service::class)->create(['organisation_id' => $organisation->id]);
        $serviceWithRelevantServiceName = factory(Service::class)->create(['name' => 'Test Name']);

        $response = $this->json('POST', '/core/v1/search', [
            'query' => 'Test Name',
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $results = json_decode($response->getContent(), true)['data'];
        $this->assertEquals($serviceWithRelevantServiceName->id, $results[0]['id']);
        $this->assertEquals($serviceWithRelevantOrganisationName->id, $results[1]['id']);
    }

    public function test_query_matches_word_from_service_description()
    {
        $service = factory(Service::class)->create([
            'description' => 'This is a service that helps to homeless find temporary housing.',
        ]);

        $response = $this->json('POST', '/core/v1/search', [
            'query' => 'homeless',
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $service->id]);
    }
}
