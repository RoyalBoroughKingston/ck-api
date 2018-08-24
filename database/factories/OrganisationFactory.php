<?php

use App\Models\Organisation;
use Faker\Generator as Faker;

$factory->define(Organisation::class, function (Faker $faker) {
    $name = $faker->unique()->company;

    return [
        'slug' => str_slug($name),
        'name' => $name,
        'description' => 'This organisation provides x service.',
        'url' => $faker->url,
        'email' => $faker->safeEmail,
        'phone' => random_uk_phone(),
    ];
});
