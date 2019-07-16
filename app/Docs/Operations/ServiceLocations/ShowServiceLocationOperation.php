<?php

namespace App\Docs\Operations\ServiceLocations;

use App\Docs\Parameters\IncludeParameter;
use App\Docs\Schemas\ResourceSchema;
use App\Docs\Schemas\ServiceLocation\ServiceLocationSchema;
use App\Docs\Tags\ServiceLocationsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ShowServiceLocationOperation extends Operation
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
            ->tags(ServiceLocationsTag::create())
            ->summary('Get a specific service location')
            ->description('**Permission:** `Open`')
            ->noSecurity()
            ->parameters(
                Parameter::path()
                    ->name('service_location')
                    ->description('The ID of the service location')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID)),
                IncludeParameter::create(null, ['location'])
            )
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        ResourceSchema::create(null, ServiceLocationSchema::create())
                    )
                )
            );
    }
}
