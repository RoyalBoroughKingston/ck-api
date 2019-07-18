<?php

namespace App\Docs\Paths\Locations;

use App\Docs\Operations\Locations\IndexLocationOperation;
use App\Docs\Operations\Locations\StoreLocationOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class LocationsRootPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/locations')
            ->operations(
                IndexLocationOperation::create(),
                StoreLocationOperation::create()
            );
    }
}
