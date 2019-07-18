<?php

namespace App\Docs\Paths\Locations;

use App\Docs\Operations\Locations\DestroyLocationOperation;
use App\Docs\Operations\Locations\ShowLocationOperation;
use App\Docs\Operations\Locations\UpdateLocationOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class LocationsNestedPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/locations/{location}')
            ->parameters(
                Parameter::path()
                    ->name('location')
                    ->description('The ID of the location')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID))
            )
            ->operations(
                ShowLocationOperation::create(),
                UpdateLocationOperation::create(),
                DestroyLocationOperation::create()
            );
    }
}
