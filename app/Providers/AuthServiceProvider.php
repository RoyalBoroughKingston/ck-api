<?php

namespace App\Providers;

use App\Models\Client;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::enableImplicitGrant();
        Passport::tokensExpireIn(now()->addMonths(18));
        Passport::useClientModel(Client::class);
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        Passport::ignoreMigrations();
    }
}
