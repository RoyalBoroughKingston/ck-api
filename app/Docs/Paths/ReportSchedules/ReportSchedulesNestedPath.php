<?php

namespace App\Docs\Paths\ReportSchedules;

use App\Docs\Operations\ReportSchedules\DestroyReportScheduleOperation;
use App\Docs\Operations\ReportSchedules\ShowReportScheduleOperation;
use App\Docs\Operations\ReportSchedules\UpdateReportScheduleOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

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
            ->operations(
                ShowReportScheduleOperation::create(),
                UpdateReportScheduleOperation::create(),
                DestroyReportScheduleOperation::create()
            );
    }
}
