<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\ServiceRefreshToken::class, function (Faker $faker) {
    return [
        'service_id' => function () {
            return factory(App\Models\Service::class)->create()->id;
        },
    ];
});
