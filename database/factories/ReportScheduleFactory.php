<?php

use App\Models\ReportSchedule;
use App\Models\ReportType;
use Faker\Generator as Faker;

$factory->define(App\Models\ReportSchedule::class, function (Faker $faker) {
    return [
        'report_type_id' => ReportType::usersExport()->id,
        'repeat_type' => array_random([ReportSchedule::REPEAT_TYPE_WEEKLY, ReportSchedule::REPEAT_TYPE_MONTHLY]),
    ];
});
