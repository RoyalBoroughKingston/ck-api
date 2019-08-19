<?php

namespace App\Docs\Schemas\ReportSchedule;

use App\Models\ReportSchedule;
use App\Models\ReportType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ReportScheduleSchema extends Schema
{
    /**
     * @param string|null $objectId
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->type(static::FORMAT_UUID)
            ->properties(
                Schema::string('id')
                    ->format(Schema::FORMAT_UUID),
                Schema::string('report_type')
                    ->enum(
                        ...ReportType::query()->pluck('name')->toArray()
                    ),
                Schema::string('repeat_type')
                    ->enum(
                        ReportSchedule::REPEAT_TYPE_WEEKLY,
                        ReportSchedule::REPEAT_TYPE_MONTHLY
                    ),
                Schema::string('created_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('updated_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable()
            );
    }
}
