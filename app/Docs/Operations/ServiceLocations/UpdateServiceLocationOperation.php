<?php

namespace App\Docs\Operations\ServiceLocations;

use App\Docs\Responses\UpdateRequestReceivedResponse;
use App\Docs\Schemas\ServiceLocation\UpdateServiceLocationSchema;
use App\Docs\Tags\ServiceLocationsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateServiceLocationOperation extends Operation
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
            ->tags(ServiceLocationsTag::create())
            ->summary('Update a specific service location')
            ->description('**Permission:** `Service Admin`')
            ->parameters(
                Parameter::path()
                    ->name('service_location')
                    ->description('The ID of the service location')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID))
            )
            ->requestBody(
                RequestBody::create()
                    ->required()
                    ->content(
                        MediaType::json()->schema(UpdateServiceLocationSchema::create())
                    )
            )
            ->responses(
                UpdateRequestReceivedResponse::create(null, UpdateServiceLocationSchema::create())
            );
    }
}
