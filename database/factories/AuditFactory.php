<?php

use App\Models\Audit;
use Faker\Generator as Faker;

$factory->define(Audit::class, function (Faker $faker) {
    return [
        'action' => array_random([Audit::ACTION_CREATE, Audit::ACTION_READ, Audit::ACTION_UPDATE, Audit::ACTION_DELETE]),
        'description' => $faker->sentence,
        'ip_address' => $faker->ipv4,
    ];
});
