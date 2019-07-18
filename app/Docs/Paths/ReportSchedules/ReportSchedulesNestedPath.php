<?php

namespace App\Docs\Paths\ReportSchedules;

use App\Docs\Operations\ReportSchedules\DestroyReportScheduleOperation;
use App\Docs\Operations\ReportSchedules\ShowReportScheduleOperation;
use App\Docs\Operations\ReportSchedules\UpdateReportScheduleOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ReportSchedulesNestedPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/report-schedules/{report_schedule}')
            ->parameters(
                Parameter::path()
                    ->name('report_schedule')
                    ->description('The ID of the report schedule')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID))
            )
            ->operations(
                ShowReportScheduleOperation::create(),
                UpdateReportScheduleOperation::create(),
                DestroyReportScheduleOperation::create()
            );
    }
}
