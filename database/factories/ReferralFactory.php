<?php

use App\Models\Referral;
use Faker\Generator as Faker;

$factory->define(Referral::class, function (Faker $faker) {
    return [
        'reference' => str_random(10),
        'status' => Referral::STATUS_NEW,
        'name' => $faker->name,
        'email' => $faker->safeEmail,
    ];
});
