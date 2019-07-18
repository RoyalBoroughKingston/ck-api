<?php

namespace App\Docs\Paths\Users;

use App\Docs\Operations\Users\DestroyUserOperation;
use App\Docs\Operations\Users\ShowUserOperation;
use App\Docs\Operations\Users\UpdateUserOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

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
            ->parameters(
                Parameter::path()
                    ->name('user')
                    ->description('The ID of the user')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID))
            )
            ->operations(
                ShowUserOperation::create(),
                UpdateUserOperation::create(),
                DestroyUserOperation::create()
            );
    }
}
