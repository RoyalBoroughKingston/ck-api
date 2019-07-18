<?php

namespace App\Docs\Operations\ServiceLocations;

use App\Docs\Responses\ResourceDeletedResponse;
use App\Docs\Tags\ServiceLocationsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;

class DestroyServiceLocationOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->action(static::ACTION_DELETE)
            ->tags(ServiceLocationsTag::create())
            ->summary('Delete a specific service location')
            ->description('**Permission:** `Super Admin`')
            ->responses(ResourceDeletedResponse::create(null, 'service location'));
    }
}
