<?php

use Faker\Generator as Faker;

$factory->define(App\Models\HolidayOpeningHour::class, function (Faker $faker) {
    return [
        'is_closed' => true,
        'starts_at' => '2018-12-23',
        'ends_at' => '2019-01-01',
        'opens_at' => '00:00:00',
        'closes_at' => '00:00:00',
    ];
});
