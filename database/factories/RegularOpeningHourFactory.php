<?php

use Faker\Generator as Faker;

$factory->define(App\Models\RegularOpeningHour::class, function (Faker $faker) {
    return [
        'frequency' => \App\Models\RegularOpeningHour::FREQUENCY_WEEKLY,
        'weekday' => \App\Models\RegularOpeningHour::WEEKDAY_MONDAY,
        'opens_at' => \App\Support\Time::create('09:00:00'),
        'closes_at' => \App\Support\Time::create('17:30:00'),
    ];
});
