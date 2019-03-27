<?php

namespace Tests;

use App\Models\Collection;
use App\Models\Service;
use App\Models\Taxonomy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;
    use WithFaker;

    /**
     * @var bool
     */
    protected static $elasticsearchInitialised = false;

    /**
     * @var \Illuminate\Support\Carbon
     */
    protected $now;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Set the log path.
        Config::set('logging.channels.single.path', storage_path('logs/testing.log'));

        // Clear the cache.
        $this->artisan('ck:redis:clear', [
            '--host' => env('REDIS_HOST'),
            '--port' => env('REDIS_PORT'),
        ]);

        // Disable the API throttle middleware.
        $this->withoutMiddleware('throttle');

        $this->setUpElasticsearch();

        $this->now = now();
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Storage::cloud()->deleteDirectory('files');

        $this->tearDownElasticsearch();

        parent::tearDown();
    }

    /**
     * Setup up the Faker instance.
     *
     * @return void
     */
    protected function setUpFaker()
    {
        $this->faker = $this->makeFaker(config('app.faker_locale'));
    }

    /**
     * Delete all the collection categories and pivot records.
     */
    protected function truncateCollectionCategories()
    {
        Collection::categories()->get()->each(function (Collection $collection) {
            $collection->collectionTaxonomies()->delete();
        });
        Collection::categories()->delete();
    }

    /**
     * Delete all the collection personas and pivot records.
     */
    protected function truncateCollectionPersonas()
    {
        Collection::personas()->get()->each(function (Collection $collection) {
            $collection->collectionTaxonomies()->delete();
        });
        Collection::personas()->delete();
    }

    /**
     * Delete all the category taxonomy records.
     */
    protected function truncateTaxonomies()
    {
        Taxonomy::category()->children->each->delete();
    }

    /**
     * Sets up the Elasticsearch indices.
     */
    protected function setUpElasticsearch()
    {
        if (!static::$elasticsearchInitialised) {
            $this->artisan('ck:reindex-elasticsearch');
            static::$elasticsearchInitialised = true;
        }
    }

    /**
     * Tears down the Elasticsearch indices.
     */
    protected function tearDownElasticsearch()
    {
        try {
            $this->artisan('scout:flush', ['model' => Service::class]);
        } catch (\Exception $exception) {
            // Do nothing.
        }
    }

    /**
     * @param \Illuminate\Foundation\Testing\TestResponse $response
     */
    protected function dumpResponse(TestResponse $response)
    {
        dump(json_decode($response->getContent(), true));
    }

    /**
     * @param \Illuminate\Foundation\Testing\TestResponse $response
     * @param string|null $key
     * @return array|string
     */
    protected function getResponseContent(TestResponse $response, string $key = null)
    {
        $content = json_decode($response->getContent(), true);

        if ($key !== null) {
            return Arr::get($content, $key);
        }

        return $content;
    }

    /**
     * Fakes events except for models.
     */
    protected function fakeEvents()
    {
        $initialDispatcher = Event::getFacadeRoot();
        Event::fake();
        Model::setEventDispatcher($initialDispatcher);
    }
}
