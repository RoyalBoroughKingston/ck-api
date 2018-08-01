<?php

namespace App\Providers;

use App\Models\Collection;
use App\Models\Location;
use App\Observers\CollectionObserver;
use App\Observers\LocationObserver;
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
