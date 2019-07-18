<?php

namespace App\Docs\Operations\Reports;

use App\Docs\Schemas\Report\ReportSchema;
use App\Docs\Schemas\ResourceSchema;
use App\Docs\Tags\ReportsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class ShowReportOperation extends Operation
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
            ->tags(ReportsTag::create())
            ->summary('Get a specific report')
            ->description('**Permission:** `Global Admin`')
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        ResourceSchema::create(null, ReportSchema::create())
                    )
                )
            );
    }
}
