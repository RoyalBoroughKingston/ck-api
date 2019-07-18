<?php

namespace App\Docs\Paths\ServiceLocations;

use App\Docs\Operations\ServiceLocations\IndexServiceLocationOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class ServiceLocationsIndexPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/service-locations/index')
            ->operations(
                IndexServiceLocationOperation::create()
                    ->action(IndexServiceLocationOperation::ACTION_POST)
                    ->description(
                        <<<'EOT'
This is an alias of `GET /service-locations` which allows all the query string parameters to be 
passed as part of the request body.
EOT
                    )
            );
    }
}
