<?php

use Faker\Generator as Faker;

$factory->define(App\Models\ServiceLocation::class, function (Faker $faker) {
    return [
        'service_id' => function () {
            return factory(\App\Models\Service::class)->create()->id;
        },
        'location_id' => function () {
            return factory(\App\Models\Location::class)->create()->id;
        },
    ];
});
