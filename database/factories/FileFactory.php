<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(App\Models\File::class, function (Faker $faker) {
    return [
        'filename' => Str::random() . '.dat',
        'mime_type' => 'text/plain',
        'is_private' => false,
    ];
});

$factory->state(App\Models\File::class, 'pending-assignment', [
    'meta' => [
        'type' => \App\Models\File::META_TYPE_PENDING_ASSIGNMENT,
    ],
]);
