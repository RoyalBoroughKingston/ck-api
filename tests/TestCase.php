<?php

namespace Tests;

use App\Models\Collection;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Config;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

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

        $this->now = now();
    }

    /**
     * Refresh a conventional test database.
     *
     * @return void
     */
    protected function refreshTestDatabase()
    {
        if (! RefreshDatabaseState::$migrated) {
            $this->artisan('migrate:fresh', [
                '--drop-views' => true,
                '--seed' => true,
            ]);

            $this->app[Kernel::class]->setArtisan(null);

            RefreshDatabaseState::$migrated = true;
        }

        $this->beginDatabaseTransaction();
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
}
