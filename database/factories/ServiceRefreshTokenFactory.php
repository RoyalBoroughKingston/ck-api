<?php

use App\Models\Audit;
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Audit::class, function (Faker $faker) {
    return [
        'service_id' => function () {
            return factory(App\Models\Service::class)->create()->id;
        },
    ];
});
