<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\TestResponse;

class TestingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot()
    {
        TestResponse::macro('assertJsonResource', function (array $data) {
            $this->assertJsonStructure(['data' => $data]);
        });

        TestResponse::macro('assertJsonCollection', function (array $data) {
            $this->assertJsonStructure([
                'data' => ['*' => $data],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'from', 'last_page', 'path', 'per_page', 'to', 'total'],
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
