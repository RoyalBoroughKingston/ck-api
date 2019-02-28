<?php

use App\Models\Audit;
use Faker\Generator as Faker;
use Illuminate\Support\Arr;

$factory->define(Audit::class, function (Faker $faker) {
    return [
        'action' => Arr::random([Audit::ACTION_CREATE, Audit::ACTION_READ, Audit::ACTION_UPDATE, Audit::ACTION_DELETE]),
        'description' => $faker->sentence,
        'ip_address' => $faker->ipv4,
    ];
});
