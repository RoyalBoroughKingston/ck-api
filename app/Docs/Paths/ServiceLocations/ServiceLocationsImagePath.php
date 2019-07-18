<?php

namespace App\Docs\Paths\ServiceLocations;

use App\Docs\Operations\ServiceLocations\ImageServiceLocationOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ServiceLocationsImagePath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/service-locations/{service_location}/image.png')
            ->parameters(
                Parameter::path()
                    ->name('service_location')
                    ->description('The ID of the service location')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID))
            )
            ->operations(
                ImageServiceLocationOperation::create()
            );
    }
}
