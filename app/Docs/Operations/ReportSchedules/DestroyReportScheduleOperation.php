<?php

namespace App\Docs\Operations\ReportSchedules;

use App\Docs\Responses\ResourceDeletedResponse;
use App\Docs\Tags\ReportSchedulesTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;

class DestroyReportScheduleOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->action(static::ACTION_DELETE)
            ->tags(ReportSchedulesTag::create())
            ->summary('Delete a specific report schedule')
            ->description('**Permission:** `Global Admin`')
            ->responses(ResourceDeletedResponse::create());
    }
}
