<?php

namespace App\Providers;

use App\Models\Collection;
use App\Models\File;
use App\Models\Location;
use App\Models\Organisation;
use App\Models\Referral;
use App\Models\Report;
use App\Models\Service;
use App\Models\ServiceLocation;
use App\Models\Taxonomy;
use App\Observers\CollectionObserver;
use App\Observers\FileObserver;
use App\Observers\LocationObserver;
use App\Observers\OrganisationObserver;
use App\Observers\ReferralObserver;
use App\Observers\ReportObserver;
use App\Observers\ServiceLocationObserver;
use App\Observers\ServiceObserver;
use App\Observers\TaxonomyObserver;
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
        File::observe(FileObserver::class);
        Location::observe(LocationObserver::class);
        Organisation::observe(OrganisationObserver::class);
        Referral::observe(ReferralObserver::class);
        Report::observe(ReportObserver::class);
        ServiceLocation::observe(ServiceLocationObserver::class);
        Service::observe(ServiceObserver::class);
        Taxonomy::observe(TaxonomyObserver::class);

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
