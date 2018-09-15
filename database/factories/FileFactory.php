<?php

use Faker\Generator as Faker;

$factory->define(App\Models\File::class, function (Faker $faker) {
    return [
        'filename' => str_random().'.dat',
        'mime_type' => 'text/plain',
        'is_private' => false,
    ];
});
