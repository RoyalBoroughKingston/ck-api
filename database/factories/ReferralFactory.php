<?php

use App\Models\Referral;
use Faker\Generator as Faker;

$factory->define(Referral::class, function (Faker $faker) {
    return [
        'service_id' => function () {
            return factory(\App\Models\Service::class)->create()->id;
        },
        'reference' => str_random(10),
        'status' => Referral::STATUS_NEW,
        'name' => $faker->name,
        'email' => $faker->safeEmail,
    ];
});
