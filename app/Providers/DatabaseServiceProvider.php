<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use PDO;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Unset the MySQL SSL certificate path if it is not provided.
        $databaseConfigOptionsKey = 'database.connections.mysql.options';
        $databaseConfigCertPathKey = sprintf('%s.%d', $databaseConfigOptionsKey, PDO::MYSQL_ATTR_SSL_CAPATH);

        if (config($databaseConfigCertPathKey) === null) {
            // Get the current database options array.
            $databaseOptions = config($databaseConfigOptionsKey);

            // Remove the certificate path from the array.
            unset($databaseOptions[PDO::MYSQL_ATTR_SSL_CAPATH]);

            // Update the database options array without the certificate path.
            config()->set($databaseConfigOptionsKey, $databaseOptions);
        }
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
