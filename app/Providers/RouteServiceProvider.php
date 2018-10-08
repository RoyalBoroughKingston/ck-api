<?php

namespace App\Providers;

use App\Models\Organisation;
use App\Models\Service;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // Resolve by ID first, then resort to slug.
        Route::bind('organisation', function ($value) {
            return Organisation::query()->find($value)
                ?? Organisation::query()->where('slug', $value)->first()
                ?? abort(Response::HTTP_NOT_FOUND);
        });

        // Resolve by ID first, then resort to slug.
        Route::bind('service', function ($value) {
            return Service::query()->find($value)
                ?? Service::query()->where('slug', $value)->first()
                ?? abort(Response::HTTP_NOT_FOUND);
        });
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapOauthRoutes();

        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }

    /**
     * Define the "OAuth" routes for the application.
     *
     * These routes extend upon the OAuth standard.
     *
     * @return void
     */
    protected function mapOauthRoutes()
    {
        Route::middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/oauth.php'));
    }
}
