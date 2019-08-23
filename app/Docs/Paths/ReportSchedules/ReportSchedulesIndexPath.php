<?php

namespace App\Docs\Paths\ReportSchedules;

use App\Docs\Operations\ReportSchedules\IndexReportScheduleOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class ReportSchedulesIndexPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/report-schedules/index')
            ->operations(
                IndexReportScheduleOperation::create()
                    ->action(IndexReportScheduleOperation::ACTION_POST)
                    ->description(
                        <<<'EOT'
This is an alias of `GET /report-schedules` which allows all the query string parameters to be 
passed as part of the request body.
EOT
                    )
            );
    }
}
