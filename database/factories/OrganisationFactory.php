<?php

use App\Models\Organisation;
use Faker\Generator as Faker;

$factory->define(Organisation::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'description' => 'This organisation provides x service.',
        'url' => $faker->url,
        'email' => $faker->safeEmail,
        'phone' => $faker->phoneNumber,
    ];
});
