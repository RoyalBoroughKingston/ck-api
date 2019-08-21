<?php

namespace App\Docs\Operations\ReportSchedules;

use App\Docs\Schemas\ReportSchedule\ReportScheduleSchema;
use App\Docs\Schemas\ReportSchedule\UpdateReportScheduleSchema;
use App\Docs\Schemas\ResourceSchema;
use App\Docs\Tags\ReportSchedulesTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class UpdateReportScheduleOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->action(static::ACTION_PUT)
            ->tags(ReportSchedulesTag::create())
            ->summary('Update a specific report schedule')
            ->description('**Permission:** `Global Admin`')
            ->requestBody(
                RequestBody::create()
                    ->required()
                    ->content(
                        MediaType::json()->schema(UpdateReportScheduleSchema::create())
                    )
            )
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        ResourceSchema::create(null, ReportScheduleSchema::create())
                    )
                )
            );
    }
}
