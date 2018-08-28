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
        'has_wheelchair_access' => false,
        'has_induction_loop' => false,
        'lat' => rand(-90, 90),
        'lon' => rand(-180, 180),
    ];
});
