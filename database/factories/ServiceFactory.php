<?php

use App\Models\Service;
use Faker\Generator as Faker;

$factory->define(Service::class, function (Faker $faker) {
    return [
        'organisation_id' => function () {
            return factory(\App\Models\Organisation::class)->create()->id;
        },
        'name' => 'Preventing Homelessness',
        'status' => Service::STATUS_ACTIVE,
        'intro' => 'This service prevents homelessness.',
        'description' => $faker->paragraph,
        'is_free' => true,
        'url' => $faker->url,
        'contact_name' => $faker->name,
        'contact_phone' => $faker->phoneNumber,
        'contact_email' => $faker->safeEmail,
        'show_referral_disclaimer' => false,
        'referral_method' => Service::REFERRAL_METHOD_NONE,
        'seo_title' => 'Preventing Homelessness',
        'seo_description' => 'This service prevents homelessness.',
    ];
});


$factory->afterCreating(Service::class, function (Service $service, Faker $faker) {
    \App\Models\ServiceCriterion::create([
        'service_id' => $service->id,
        'age_group' => null,
        'disability' => null,
        'employment' => null,
        'gender' => null,
        'housing' => null,
        'income' => null,
        'language' => null,
        'other' => null,
    ]);
});
