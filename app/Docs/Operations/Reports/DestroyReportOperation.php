<?php

namespace App\Docs\Operations\Reports;

use App\Docs\Responses\ResourceDeletedResponse;
use App\Docs\Schemas\Report\ReportSchema;
use App\Docs\Schemas\ResourceSchema;
use App\Docs\Tags\ReportsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class DestroyReportOperation extends Operation
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
            ->tags(ReportsTag::create())
            ->summary('Delete a specific report')
            ->description('**Permission:** `Global Admin`')
            ->parameters(
                Parameter::path()
                    ->name('report')
                    ->description('The ID of the report')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID))
            )
            ->responses(ResourceDeletedResponse::create(null, 'report'));
    }
}
