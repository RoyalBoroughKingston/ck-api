<?php

namespace App\Docs\Paths\Users;

use App\Docs\Operations\Users\IndexUserOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class UsersIndexPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/users/index')
            ->operations(
                IndexUserOperation::create()
                    ->action(IndexUserOperation::ACTION_POST)
                    ->description(
                        <<<'EOT'
This is an alias of `GET /users` which allows all the query string parameters to be passed as part 
of the request body.
EOT
                    )
            );
    }
}
