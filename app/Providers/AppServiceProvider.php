<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
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
        Blueprint::macro('foreignUuid', function (string $column, string $referencesTable, string $referencedColumn = 'id', bool $nullable = false) {
            $this->uuid($column)->nullable($nullable);
            $this->foreign($column)->references($referencedColumn)->on($referencesTable);
        });

        Blueprint::macro('nullableForeignUuid', function (string $column, string $referencesTable, string $referencedColumn = 'id') {
            $this->foreignUuid($column, $referencesTable, $referencedColumn, true);
        });

        Blueprint::macro('morphsUuid', function (string $name, string $indexName = null) {
            $this->string("{$name}_type");

            $this->uuid("{$name}_id");

            $this->index(["{$name}_type", "{$name}_id"], $indexName);
        });
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
