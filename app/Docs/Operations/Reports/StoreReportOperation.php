<?php

namespace App\Docs\Operations\Reports;

use App\Docs\Schemas\Report\ReportSchema;
use App\Docs\Schemas\Report\StoreReportSchema;
use App\Docs\Schemas\ResourceSchema;
use App\Docs\Tags\ReportsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class StoreReportOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->action(static::ACTION_POST)
            ->tags(ReportsTag::create())
            ->summary('Create a report')
            ->description('**Permission:** `Global Admin`')
            ->requestBody(
                RequestBody::create()
                    ->required()
                    ->content(
                        MediaType::json()->schema(StoreReportSchema::create())
                    )
            )
            ->responses(
                Response::created()->content(
                    MediaType::json()->schema(
                        ResourceSchema::create(null, ReportSchema::create())
                    )
                )
            );
    }
}
