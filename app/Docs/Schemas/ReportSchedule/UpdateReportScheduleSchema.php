<?php

namespace App\Docs\Schemas\ReportSchedule;

use App\Models\ReportSchedule;
use App\Models\ReportType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateReportScheduleSchema extends Schema
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->type(static::FORMAT_UUID)
            ->required('report_type', 'repeat_type')
            ->properties(
                Schema::string('report_type')
                    ->enum(
                        ...ReportType::query()->pluck('name')->toArray()
                    ),
                Schema::string('repeat_type')
                    ->enum(
                        ReportSchedule::REPEAT_TYPE_WEEKLY,
                        ReportSchedule::REPEAT_TYPE_MONTHLY
                    )
            );
    }
}
