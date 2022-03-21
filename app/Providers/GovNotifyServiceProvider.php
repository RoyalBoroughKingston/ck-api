<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class GovNotifyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot()
    {
        $this->app->singleton(\Alphagov\Notifications\Client::class, function () {
            return new \Alphagov\Notifications\Client([
                'apiKey' => config('ck.gov_notify_api_key'),
                'httpClient' => new \Http\Adapter\Guzzle7\Client()
            ]);
        });
    }

    /**
     * Register services.
     */
    public function register()
    {
        //
    }
}
