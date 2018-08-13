<?php

namespace Tests;

use App\IndexConfigurators\ServicesIndexConfigurator;
use App\Models\Collection;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Throwable;

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
    public function setUp()
    {
        parent::setUp();

        // Set the log path.
        Config::set('logging.channels.single.path', storage_path('logs/testing.log'));

        // Disable the API throttle middleware.
        $this->withoutMiddleware('throttle');

        $this->setUpElasticsearch();

        $this->now = now();
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
     * Sets up the Elasticsearch indices.
     */
    protected function setUpElasticsearch()
    {
        if (!static::$elasticsearchInitialised) {
            try {
                $this->artisan('elastic:drop-index', ['index-configurator' => ServicesIndexConfigurator::class]);
            } catch (Throwable $exception) {
                // If the index already does not exist then do nothing.
            }
            $this->artisan('elastic:create-index', ['index-configurator' => ServicesIndexConfigurator::class]);
            $this->artisan('elastic:update-mapping', ['model' => Service::class]);

            static::$elasticsearchInitialised = true;
        }

        $this->artisan('scout:flush', ['model' => Service::class]);
    }
}
