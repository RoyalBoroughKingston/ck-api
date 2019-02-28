<?php

namespace App\Console\Commands\Ck\Redis;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Predis\Command\ServerFlushDatabase;

class ClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ck:redis:clear
                            { --host= : The redis host to use }
                            { --port= : The redis port to use }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush the application redis cache';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('host') !== null) {
            $this->setHost();
        }

        if ($this->option('port') !== null) {
            $this->setPort();
        }

        $flushDbCommand = new ServerFlushDatabase();

        foreach (Redis::connection()->getConnection() as $node) {
            /** @var \Predis\Connection\ConnectionInterface $node */
            $node->executeCommand($flushDbCommand);
        }

        $this->info('Application cache cleared!');
    }

    /**
     * Set the Redis host to use.
     */
    protected function setHost()
    {
        $host = $this->option('host');

        if (config('database.redis.options.cluster') === false) {
            // Single node setup.
            config()->set('database.redis.default.host', $host);
        } else {
            // Cluster setup.
            config()->set('database.redis.clusters.default.0.host', $host);
        }
    }

    /**
     * Set the Redis port to use.
     */
    protected function setPort()
    {
        $port = $this->option('port');

        if (config('database.redis.options.cluster') === false) {
            // Single node setup.
            config()->set('database.redis.default.port', $port);
        } else {
            // Cluster setup.
            config()->set('database.redis.clusters.default.0.port', $port);
        }
    }
}
