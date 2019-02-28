<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(App\Models\File::class, function (Faker $faker) {
    return [
        'filename' => Str::random().'.dat',
        'mime_type' => 'text/plain',
        'is_private' => false,
    ];
});
