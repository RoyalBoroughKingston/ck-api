<?php

namespace App\Docs\Paths\StatusUpdates;

use App\Docs\Operations\StatusUpdates\IndexStatusUpdateOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class StatusUpdatesIndexPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/status-updates/index')
            ->operations(
                IndexStatusUpdateOperation::create()
                    ->action(IndexStatusUpdateOperation::ACTION_POST)
                    ->description(
                        <<<'EOT'
This is an alias of `GET /status-updates` which allows all the query string parameters to be passed 
as part of the request body.
EOT
                    )
            );
    }
}
