<?php

use App\Models\Service;
use Faker\Generator as Faker;

$factory->define(Service::class, function (Faker $faker) {
    return [
        'name' => 'Preventing Homelessness',
        'status' => Service::STATUS_ACTIVE,
        'intro' => 'This service prevents homelessness.',
        'description' => $faker->paragraph,
        'if_free' => true,
        'url' => $faker->url,
        'contact_name' => $faker->name,
        'contact_phone' => $faker->phoneNumber,
        'contact_email' => $faker->safeEmail,
        'accreditation_logos' => [],
        'show_referral_disclaimer' => false,
        'referral_method' => Service::REFERRAL_METHOD_NONE,
        'seo_title' => 'Preventing Homelessness',
        'seo_description' => 'This service prevents homelessness.',
    ];
});
