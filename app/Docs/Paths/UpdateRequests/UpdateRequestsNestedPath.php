<?php

namespace App\Docs\Paths\UpdateRequests;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class UpdateRequestsNestedPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/update-requests/{update_request}')
            ->operations(
                //
            );
    }
}
