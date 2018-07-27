<?php

use App\Models\Location;
use Faker\Generator as Faker;

$factory->define(Location::class, function (Faker $faker) {
    return [
        'address_line_1' => $faker->streetAddress,
        'city' => $faker->city,
        'county' => 'West Yorkshire',
        'postcode' => $faker->postcode,
        'country' => 'United Kingdom',
        'lat' => $faker->latitude,
        'lon' => $faker->longitude,
    ];
});
