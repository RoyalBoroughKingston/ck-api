<?php

namespace App\Docs\Paths\ServiceLocations;

use App\Docs\Operations\ServiceLocations\DestroyServiceLocationOperation;
use App\Docs\Operations\ServiceLocations\ShowServiceLocationOperation;
use App\Docs\Operations\ServiceLocations\UpdateServiceLocationOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class ServiceLocationsNestedPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/service-locations/{service_location}')
            ->operations(
                ShowServiceLocationOperation::create(),
                UpdateServiceLocationOperation::create(),
                DestroyServiceLocationOperation::create()
            );
    }
}
