<?php

namespace App\Providers;

use App\Queue\Connectors\SqsConnector;
use Illuminate\Queue\QueueServiceProvider as BaseQueueServiceProvider;

class QueueServiceProvider extends BaseQueueServiceProvider
{
    /**
     * Register the Amazon SQS queue connector.
     *
     * @param  \Illuminate\Queue\QueueManager  $manager
     * @return void
     */
    protected function registerSqsConnector($manager)
    {
        $manager->addConnector('sqs', function () {
            return new SqsConnector();
        });
    }
}
