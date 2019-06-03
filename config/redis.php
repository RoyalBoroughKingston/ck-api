<?php

$redis = [

    'client' => env('REDIS_CLIENT', 'predis'),

    // For single node setup.
    'default' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', 6379),
    ],

    // For clustered setup.
    'clusters' => [
        'default' => [
            'parameters' => [
                'host' => env('REDIS_HOST', '127.0.0.1'),
                'password' => env('REDIS_PASSWORD', null),
                'port' => env('REDIS_PORT', 6379),
            ],
        ],
    ],

    'options' => [
        'cluster' => env('REDIS_CLUSTER', false),

        'ssl' => [
            'verify_peer' => false,
        ],
    ],

];

if (env('REDIS_CLUSTER', false)) {
    // If in cluster mode.
    unset($redis['default']);
} else {
    // If in single node mode.
    unset($redis['clusters']);
}

return $redis;
