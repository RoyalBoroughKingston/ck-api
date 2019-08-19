<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class CloudFoundryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot()
    {
        /** @var string|null $config */
        $config = Config::get('cloudfoundry.vcap_services');

        // Skip overriding config if not running in CloudFoundry environments.
        if ($config === null) {
            return;
        }

        // Decode the JSON provided by Cloud Foundry.
        $config = json_decode($config, true);

        /** @var array $mysqlConfig */
        $mysqlConfig = $config['mysql'][0]['credentials'];

        /** @var array $redisConfig */
        $redisConfig = $config['redis'][0]['credentials'];

        /** @var array $elasticsearchConfig */
        $elasticsearchConfig = $config['elasticsearch'][0]['credentials'];

        // Set the MySQL config.
        Config::set('database.connections.mysql.host', $mysqlConfig['host']);
        Config::set('database.connections.mysql.port', $mysqlConfig['port']);
        Config::set('database.connections.mysql.database', $mysqlConfig['name']);
        Config::set('database.connections.mysql.username', $mysqlConfig['username']);
        Config::set('database.connections.mysql.password', $mysqlConfig['password']);

        // Set the Redis config.
        Config::set('database.redis.default.host', $redisConfig['host']);
        Config::set('database.redis.default.password', $redisConfig['password']);
        Config::set('database.redis.default.port', $redisConfig['port']);

        // Set the Elasticsearch config.
        Config::set('scout_elastic.client.hosts.0', $elasticsearchConfig['uri']);
    }

    /**
     * Register services.
     */
    public function register()
    {
        //
    }
}
