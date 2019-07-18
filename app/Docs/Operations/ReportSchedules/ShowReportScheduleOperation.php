<?php

namespace App\Docs\Operations\ReportSchedules;

use App\Docs\Schemas\ReportSchedule\ReportScheduleSchema;
use App\Docs\Schemas\ResourceSchema;
use App\Docs\Tags\ReportSchedulesTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class ShowReportScheduleOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->action(static::ACTION_GET)
            ->tags(ReportSchedulesTag::create())
            ->summary('Get a specific report schedule')
            ->description('**Permission:** `Global Admin`')
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        ResourceSchema::create(null, ReportScheduleSchema::create())
                    )
                )
            );
    }
}
