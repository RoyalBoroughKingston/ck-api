<?php

namespace App\Docs\Operations\ReportSchedules;

use App\Docs\Parameters\FilterIdParameter;
use App\Docs\Parameters\PageParameter;
use App\Docs\Parameters\PerPageParameter;
use App\Docs\Schemas\PaginationSchema;
use App\Docs\Schemas\ReportSchedule\ReportScheduleSchema;
use App\Docs\Tags\ReportSchedulesTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class IndexReportScheduleOperation extends Operation
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
            ->summary('List all the report schedules')
            ->description(
                <<<'EOT'
**Permission:** `Global Admin`

---

Report schedules are returned in descending order of the date they were created.
EOT
            )
            ->parameters(
                PageParameter::create(),
                PerPageParameter::create(),
                FilterIdParameter::create()
            )
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        PaginationSchema::create(null, ReportScheduleSchema::create())
                    )
                )
            );
    }
}
