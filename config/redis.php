<?php

$redis = [

    'client' => 'predis',
    'cluster' => env('REDIS_CLUSTER', false),

    // For single node setup.
    'default' => [
        'scheme' => env('REDIS_SCHEME', 'tcp'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', 6379),
        'database' => 0,
    ],

    'queue' => [
        'scheme' => env('REDIS_SCHEME', 'tcp'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', 6379),
        'database' => 1,
    ],

    'session' => [
        'scheme' => env('REDIS_SCHEME', 'tcp'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', 6379),
        'database' => 2,
    ],

    // For clustered setup.
    'clusters' => [
        'default' => [
            [
                'scheme' => env('REDIS_SCHEME', 'tcp'),
                'host' => env('REDIS_HOST', '127.0.0.1'),
                'password' => env('REDIS_PASSWORD', null),
                'port' => env('REDIS_PORT', 6379),
                'database' => 0,
            ]
        ],

        'queue' => [
            [
                'scheme' => env('REDIS_SCHEME', 'tcp'),
                'host' => env('REDIS_HOST', '127.0.0.1'),
                'password' => env('REDIS_PASSWORD', null),
                'port' => env('REDIS_PORT', 6379),
                'database' => 1,
            ]
        ],

        'session' => [
            [
                'scheme' => env('REDIS_SCHEME', 'tcp'),
                'host' => env('REDIS_HOST', '127.0.0.1'),
                'password' => env('REDIS_PASSWORD', null),
                'port' => env('REDIS_PORT', 6379),
                'database' => 2,
            ]
        ],

        'options' => [
            'cluster' => 'redis',
        ],
    ],

    'options' => [
        'parameters' => [
            'scheme' => env('REDIS_SCHEME', 'tcp'),
            'password' => env('REDIS_PASSWORD', null),
        ],

        'ssl' => [
            'verify_peer' => false,
        ],
    ],

];

if (env('REDIS_CLUSTER', false)) {
    unset($redis['default']);
    unset($redis['queue']);
    unset($redis['session']);
} else {
    unset($redis['clusters']);
}

return $redis;
