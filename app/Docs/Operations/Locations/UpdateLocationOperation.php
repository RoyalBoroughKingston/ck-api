<?php

namespace App\Docs\Operations\Locations;

use App\Docs\Responses\UpdateRequestReceivedResponse;
use App\Docs\Schemas\Location\LocationSchema;
use App\Docs\Schemas\Location\UpdateLocationSchema;
use App\Docs\Tags\LocationsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateLocationOperation extends Operation
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
            ->tags(LocationsTag::create())
            ->summary('Update a specific location')
            ->description('**Permission:** `Service Admin`')
            ->parameters(
                Parameter::path()
                    ->name('location')
                    ->description('The ID of the location')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID))
            )
            ->requestBody(
                RequestBody::create()->content(
                    MediaType::json()->schema(UpdateLocationSchema::create())
                )
            )
            ->responses(
                UpdateRequestReceivedResponse::create(null, LocationSchema::create())
            );
    }
}
