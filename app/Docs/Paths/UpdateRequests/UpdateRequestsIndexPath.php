<?php

namespace App\Docs\Paths\UpdateRequests;

use App\Docs\Operations\UpdateRequests\IndexUpdateRequestOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class UpdateRequestsIndexPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/update-requests/index')
            ->operations(
                IndexUpdateRequestOperation::create()
                    ->action(IndexUpdateRequestOperation::ACTION_POST)
                    ->description(
                        <<<'EOT'
This is an alias of `GET /update-requests` which allows all the query string parameters to be passed 
as part of the request body.
EOT
                    )
            );
    }
}
