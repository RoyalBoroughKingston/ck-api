<?php

namespace App\Docs\Schemas\User;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UserSchema extends Schema
{
    /**
     * @param string|null $objectId
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->type(static::TYPE_OBJECT)
            ->properties(
                Schema::string('id')
                    ->format(Schema::FORMAT_UUID)
                    ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b'),
                Schema::string('first_name')
                    ->example('John'),
                Schema::string('last_name')
                    ->example('Doe'),
                Schema::string('email')
                    ->example('john.doe@example.com'),
                Schema::string('phone')
                    ->example('07700000000'),
                Schema::array('roles')
                    ->items(RoleSchema::create()),
                Schema::string('created_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('updated_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('deleted_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable()
                    ->example(null)
            );
    }
}
