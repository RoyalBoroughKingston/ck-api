<?php

use App\Models\Taxonomy;
use Faker\Generator as Faker;

$factory->define(Taxonomy::class, function (Faker $faker) {
    $name = $faker->unique()->words(3, true);

    return [
        'name' => $name,
        'parent_id' => Taxonomy::category()->children()->first()->id,
        'order' => 0,
        'depth' => 2,
    ];
});

$factory->state(Taxonomy::class, 'lga-standards', [
    'parent_id' => function () {
        return Taxonomy::category()->children()->where('name', 'LGA Standards')->value('id');
    },

]);

$factory->state(Taxonomy::class, 'open-active', [
    'parent_id' => function () {
        return Taxonomy::category()->children()->where('name', 'OpenActive')->value('id');
    },
]);
