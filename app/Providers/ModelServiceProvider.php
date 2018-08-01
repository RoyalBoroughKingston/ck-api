<?php

namespace App\Providers;

use App\Models\Collection;
use App\Models\Location;
use App\Models\Organisation;
use App\Models\Service;
use App\Models\ServiceLocation;
use App\Observers\CollectionObserver;
use App\Observers\LocationObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class ModelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Collection::observe(CollectionObserver::class);
        Location::observe(LocationObserver::class);

        Relation::morphMap([
            'locations' => Location::class,
            'services' => Service::class,
            'service-locations' => ServiceLocation::class,
            'organisations' => Organisation::class,
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
