<?php

namespace App\Providers;

use App\Contracts\Geocoder;
use App\Geocode\GoogleGeocoder;
use App\Geocode\StubGeocoder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        switch (config('ck.geocode_driver')) {
            case 'google':
                $this->app->bind(Geocoder::class, GoogleGeocoder::class);
                break;
            case 'stub':
            default:
                $this->app->bind(Geocoder::class, StubGeocoder::class);
                break;
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
