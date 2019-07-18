<?php

namespace App\Docs\Paths\Users;

use App\Docs\Operations\Users\DestroyUserOperation;
use App\Docs\Operations\Users\ShowUserOperation;
use App\Docs\Operations\Users\UpdateUserOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class UsersNestedPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/users/{user}')
            ->operations(
                ShowUserOperation::create(),
                UpdateUserOperation::create(),
                DestroyUserOperation::create()
            );
    }
}
