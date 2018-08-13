<?php

namespace Tests;

use App\Models\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;
    use WithFaker;

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
}
