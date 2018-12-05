<?php

use Faker\Generator as Faker;

$factory->define(App\Models\PageFeedback::class, function (Faker $faker) {
    return [
        'url' => $faker->url,
        'feedback' => $faker->sentence,
    ];
});
