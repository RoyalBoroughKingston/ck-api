<?php

namespace App\Docs\Operations\Locations;

use App\Docs\Parameters\MaxDimensionParameter;
use App\Docs\Parameters\UpdateRequestIdParameter;
use App\Docs\Responses\PngResponse;
use App\Docs\Tags\LocationsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;

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
                MaxDimensionParameter::create(),
                UpdateRequestIdParameter::create()
            )
            ->responses(PngResponse::create());
    }
}
