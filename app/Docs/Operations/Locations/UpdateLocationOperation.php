<?php

namespace App\Docs\Operations\Locations;

use App\Docs\Responses\UpdateRequestReceivedResponse;
use App\Docs\Schemas\Location\UpdateLocationSchema;
use App\Docs\Tags\LocationsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;

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
            ->requestBody(
                RequestBody::create()->content(
                    MediaType::json()->schema(UpdateLocationSchema::create())
                )
            )
            ->responses(
                UpdateRequestReceivedResponse::create(null, UpdateLocationSchema::create())
            );
    }
}
