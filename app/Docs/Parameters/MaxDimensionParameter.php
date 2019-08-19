<?php

namespace App\Docs\Parameters;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class MaxDimensionParameter extends Parameter
{
    /**
     * @param string|null $objectId
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->in(static::IN_QUERY)
            ->name('max_dimension')
            ->description('The maximum dimension to resize the image to (preserves aspect ratio)')
            ->schema(
                Schema::integer()
                    ->minimum(1)
                    ->maximum(1000)
            );
    }
}
