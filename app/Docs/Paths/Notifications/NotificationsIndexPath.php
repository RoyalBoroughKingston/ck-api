<?php

namespace App\Docs\Paths\Notifications;

use App\Docs\Operations\Notifications\IndexNotificationOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class NotificationsIndexPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/notifications/index')
            ->operations(
                IndexNotificationOperation::create()
                    ->action(IndexNotificationOperation::ACTION_POST)
                    ->description(
                        <<<'EOT'
This is an alias of `GET /notifications` which allows all the query string parameters to be passed 
as part of the request body.
EOT
                    )
            );
    }
}
