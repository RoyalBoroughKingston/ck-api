<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\Organisation;
use App\Models\Service;
use App\Models\ServiceLocation;
use App\Models\Taxonomy;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SearchTest extends TestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->truncateTaxonomies();
        $this->truncateCollectionCategories();
        $this->truncateCollectionPersonas();
    }

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
        $taxonomy = Taxonomy::category()->children()->create(['name' => 'PHPUnit Taxonomy', 'order' => 1]);
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

    public function test_filter_by_category_works()
    {
        $service = factory(Service::class)->create();
        $collection = Collection::create(['type' => Collection::TYPE_CATEGORY, 'name' => 'Self Help', 'meta' => [], 'order' => 1]);
        $taxonomy = Taxonomy::category()->children()->create(['name' => 'PHPUnit Taxonomy', 'order' => 1]);
        $collection->collectionTaxonomies()->create(['taxonomy_id' => $taxonomy->id]);
        $service->serviceTaxonomies()->create(['taxonomy_id' => $taxonomy->id]);
        $service->save();

        $response = $this->json('POST', '/core/v1/search', [
            'category' => $collection->name,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $service->id]);
    }

    public function test_filter_by_persona_works()
    {
        $service = factory(Service::class)->create();
        $collection = Collection::create(['type' => Collection::TYPE_PERSONA, 'name' => 'Refugees', 'meta' => [], 'order' => 1]);
        $taxonomy = Taxonomy::category()->children()->create(['name' => 'PHPUnit Taxonomy', 'order' => 1]);
        $collection->collectionTaxonomies()->create(['taxonomy_id' => $taxonomy->id]);
        $service->serviceTaxonomies()->create(['taxonomy_id' => $taxonomy->id]);
        $service->save();

        $response = $this->json('POST', '/core/v1/search', [
            'persona' => $collection->name,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $service->id]);
    }

    public function test_filter_by_wait_time_works()
    {
        $oneMonthWaitTimeService = factory(Service::class)->create(['wait_time' => Service::WAIT_TIME_MONTH]);
        $twoWeeksWaitTimeService = factory(Service::class)->create(['wait_time' => Service::WAIT_TIME_TWO_WEEKS]);
        $oneWeekWaitTimeService = factory(Service::class)->create(['wait_time' => Service::WAIT_TIME_ONE_WEEK]);

        $response = $this->json('POST', '/core/v1/search', [
            'wait_time' => Service::WAIT_TIME_TWO_WEEKS,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $oneWeekWaitTimeService->id]);
        $response->assertJsonFragment(['id' => $twoWeeksWaitTimeService->id]);
        $response->assertJsonMissing(['id' => $oneMonthWaitTimeService->id]);
    }

    public function test_filter_by_is_free_works()
    {
        $paidService = factory(Service::class)->create(['is_free' => false]);
        $freeService = factory(Service::class)->create(['is_free' => true]);

        $response = $this->json('POST', '/core/v1/search', [
            'is_free' => true,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $freeService->id]);
        $response->assertJsonMissing(['id' => $paidService->id]);
    }

    public function test_order_by_location_works()
    {
        $service = factory(Service::class)->create();
        $serviceLocation = factory(ServiceLocation::class)->create(['service_id' => $service->id]);
        DB::table('locations')->where('id', $serviceLocation->location->id)->update(['lat' => 15, 'lon' => 15]);
        $service->save();

        $service2 = factory(Service::class)->create();
        $serviceLocation2 = factory(ServiceLocation::class)->create(['service_id' => $service2->id]);
        DB::table('locations')->where('id', $serviceLocation2->location->id)->update(['lat' => 20, 'lon' => 20]);
        $service2->save();

        $service3 = factory(Service::class)->create();
        $serviceLocation3 = factory(ServiceLocation::class)->create(['service_id' => $service3->id]);
        DB::table('locations')->where('id', $serviceLocation3->location->id)->update(['lat' => 30, 'lon' => 30]);
        $service3->save();

        $response = $this->json('POST', '/core/v1/search', [
            'order' => 'distance',
            'location' => [
                'lat' => 20,
                'lon' => 20,
            ],
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $service2->id]);
        $hits = json_decode($response->getContent(), true)['data'];
        $this->assertEquals($service2->id, $hits[0]['id']);
        $this->assertEquals($service->id, $hits[1]['id']);
        $this->assertEquals($service3->id, $hits[2]['id']);
    }

    public function test_query_and_filter_works()
    {
        $service = factory(Service::class)->create(['name' => 'Ayup Digital']);
        $collection = Collection::create(['type' => Collection::TYPE_CATEGORY, 'name' => 'Self Help', 'meta' => [], 'order' => 1]);
        $taxonomy = Taxonomy::category()->children()->create(['name' => 'Collection', 'order' => 1]);
        $collectionTaxonomy = $collection->collectionTaxonomies()->create(['taxonomy_id' => $taxonomy->id]);
        $service->serviceTaxonomies()->create(['taxonomy_id' => $taxonomy->id]);
        $service->save();

        $differentService = factory(Service::class)->create(['name' => 'Ayup Digital']);
        $differentCollection = Collection::create(['type' => Collection::TYPE_PERSONA, 'name' => 'Refugees', 'meta' => [], 'order' => 1]);
        $differentTaxonomy = Taxonomy::category()->children()->create(['name' => 'Persona', 'order' => 2]);
        $differentCollection->collectionTaxonomies()->create(['taxonomy_id' => $differentTaxonomy->id]);
        $differentService->serviceTaxonomies()->create(['taxonomy_id' => $differentTaxonomy->id]);
        $differentService->save();

        $response = $this->json('POST', '/core/v1/search', [
            'query' => 'Ayup Digital',
            'category' => $collectionTaxonomy->collection->name,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $service->id]);
        $response->assertJsonMissing(['id' => $differentService->id]);
    }

    public function test_query_and_filter_works_when_query_does_not_match()
    {
        $service = factory(Service::class)->create(['name' => 'Ayup Digital']);
        $collection = Collection::create(['type' => Collection::TYPE_CATEGORY, 'name' => 'Self Help', 'meta' => [], 'order' => 1]);
        $taxonomy = Taxonomy::category()->children()->create(['name' => 'Collection', 'order' => 1]);
        $collectionTaxonomy = $collection->collectionTaxonomies()->create(['taxonomy_id' => $taxonomy->id]);
        $service->serviceTaxonomies()->create(['taxonomy_id' => $taxonomy->id]);
        $service->save();

        $differentService = factory(Service::class)->create(['name' => 'Ayup Digital']);
        $differentCollection = Collection::create(['type' => Collection::TYPE_PERSONA, 'name' => 'Refugees', 'meta' => [], 'order' => 1]);
        $differentTaxonomy = Taxonomy::category()->children()->create(['name' => 'Persona', 'order' => 2]);
        $differentCollection->collectionTaxonomies()->create(['taxonomy_id' => $differentTaxonomy->id]);
        $differentService->serviceTaxonomies()->create(['taxonomy_id' => $differentTaxonomy->id]);
        $differentService->save();

        $response = $this->json('POST', '/core/v1/search', [
            'query' => 'asfkjbadsflksbdafklhasdbflkbs',
            'category' => $collectionTaxonomy->collection->name,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonMissing(['id' => $service->id]);
        $response->assertJsonMissing(['id' => $differentService->id]);
    }

    public function test_only_active_services_returned()
    {
        $activeService = factory(Service::class)->create([
            'name' => 'Testing Service',
            'status' => Service::STATUS_ACTIVE,
        ]);
        $inactiveService = factory(Service::class)->create([
            'name' => 'Testing Service',
            'status' => Service::STATUS_INACTIVE,
        ]);

        $response = $this->json('POST', '/core/v1/search', [
            'query' => 'Testing Service',
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id' => $activeService->id]);
        $response->assertJsonMissing(['id' => $inactiveService->id]);
    }
}
