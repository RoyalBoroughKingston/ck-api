<?php

namespace App\Providers;

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
        // Geocode.
        switch (config('ck.geocode_driver')) {
            case 'google':
                $this->app->singleton(\App\Contracts\Geocoder::class, \App\Geocode\GoogleGeocoder::class);
                break;
            case 'nominatim':
                $this->app->singleton(\App\Contracts\Geocoder::class, \App\Geocode\NominatimGeocoder::class);
                break;
            case 'stub':
            default:
                $this->app->singleton(\App\Contracts\Geocoder::class, \App\Geocode\StubGeocoder::class);
                break;
        }

        // Search.
        switch (config('scout.driver')) {
            case 'elastic':
            default:
                $this->app->singleton(\App\Contracts\Search::class, \App\Search\ElasticsearchSearch::class);
                break;
        }

        // Email Sender.
        $this->app->singleton(\App\Contracts\EmailSender::class, \App\EmailSenders\LogEmailSender::class);
        $this->app->singleton(\App\Contracts\SmsSender::class, \App\SmsSenders\LogSmsSender::class);
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
