<?php

use Faker\Generator as Faker;

$factory->define(App\Models\RegularOpeningHour::class, function (Faker $faker) {
    return [
        'frequency' => \App\Models\RegularOpeningHour::FREQUENCY_WEEKLY,
        'weekday' => \App\Models\RegularOpeningHour::WEEKDAY_MONDAY,
        'opens_at' => '09:00:00',
        'closes_at' => '17:30:00',
    ];
});
