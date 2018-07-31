<?php

namespace App\Providers;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\ServiceProvider;

class TestingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
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
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
