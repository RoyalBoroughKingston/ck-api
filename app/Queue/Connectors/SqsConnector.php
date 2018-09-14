<?php

namespace App\Queue\Connectors;

use App\Queue\SqsQueue;
use Aws\Sqs\SqsClient;
use Illuminate\Queue\Connectors\SqsConnector as BaseSqsConnector;
use Illuminate\Support\Arr;

class SqsConnector extends BaseSqsConnector
{
    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        $config = $this->getDefaultConfiguration($config);

        if ($config['key'] && $config['secret']) {
            $config['credentials'] = Arr::only($config, ['key', 'secret', 'token']);
        }

        return new SqsQueue(
            new SqsClient($config),
            $config['queue'],
            $config['prefix'] ?? ''
        );
    }
}
