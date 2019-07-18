<?php

namespace App\Docs\Paths\Locations;

use App\Docs\Operations\Locations\IndexLocationOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class LocationsIndexPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/locations/index')
            ->operations(
                IndexLocationOperation::create()
                    ->action(IndexLocationOperation::ACTION_POST)
                    ->description(
                        <<<'EOT'
This is an alias of `GET /locations` which allows all the query string parameters to be passed as 
part of the request body.
EOT
                    )
            );
    }
}
