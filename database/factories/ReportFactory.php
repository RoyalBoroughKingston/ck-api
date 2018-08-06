<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Report::class, function (Faker $faker) {
    return [
        'report_type_id' => \App\Models\ReportType::commissionersReport()->id,
        'file_id' => function () {
            return factory(\App\Models\File::class)->create()->id;
        },
    ];
});
