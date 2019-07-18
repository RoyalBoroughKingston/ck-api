<?php

namespace App\Docs\Operations\Services;

use App\Docs\Responses\ResourceDeletedResponse;
use App\Docs\Tags\ServicesTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;

class DestroyServiceOperation extends Operation
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
            ->tags(ServicesTag::create())
            ->summary('Delete a specific service')
            ->description('**Permission:** `Global Admin`')
            ->responses(ResourceDeletedResponse::create());
    }
}
