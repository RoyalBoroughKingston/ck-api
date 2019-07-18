<?php

namespace App\Docs\Operations\UpdateRequests;

use App\Docs\Responses\ResourceDeletedResponse;
use App\Docs\Tags\UpdateRequestsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;

class DestroyUpdateRequestOperation extends Operation
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
            ->tags(UpdateRequestsTag::create())
            ->summary('Delete a specific update request')
            ->description('**Permission:** `Global Admin`')
            ->responses(
                ResourceDeletedResponse::create(null, 'update request')
            );
    }
}
