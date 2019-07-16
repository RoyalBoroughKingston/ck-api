<?php

namespace App\Docs\Operations\Locations;

use App\Docs\Parameters\MaxDimensionParameter;
use App\Docs\Parameters\UpdateRequestIdParameter;
use App\Docs\Schemas\Location\LocationSchema;
use App\Docs\Tags\LocationsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ImageLocationOperation extends Operation
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
            ->tags(LocationsTag::create())
            ->summary("Get a specific location's image")
            ->description('**Permission:** `Open`')
            ->noSecurity()
            ->parameters(
                Parameter::path()
                    ->name('location')
                    ->description('The ID of the location')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID)),
                MaxDimensionParameter::create(),
                UpdateRequestIdParameter::create()
            )
            ->responses(
                Response::ok()->content(
                    MediaType::png()->schema(
                        Schema::string()->format(Schema::FORMAT_BINARY)
                    )
                )
            );
    }
}
