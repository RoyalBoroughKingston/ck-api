<?php

namespace App\Docs\Schemas\User;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateUserSchema extends Schema
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->type(static::TYPE_OBJECT)
            ->required('first_name', 'last_name', 'email', 'phone', 'roles')
            ->properties(
                Schema::string('first_name')
                    ->example('John'),
                Schema::string('last_name')
                    ->example('Doe'),
                Schema::string('email')
                    ->example('john.doe@example.com'),
                Schema::string('phone')
                    ->example('07700000000'),
                Schema::array('roles')
                    ->items(
                        RoleSchema::create()
                            ->required('role', 'organisation_id', 'service_id')
                    )
            );
    }
}