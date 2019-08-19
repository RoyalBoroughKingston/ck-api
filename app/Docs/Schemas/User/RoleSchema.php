<?php

namespace App\Docs\Schemas\User;

use App\Models\Role;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class RoleSchema extends Schema
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
                Schema::string('role')
                    ->enum(
                        ...Role::query()->pluck('name')->toArray()
                    ),
                Schema::string('organisation_id')
                    ->format(Schema::FORMAT_UUID)
                    ->nullable(),
                Schema::string('service_id')
                    ->format(Schema::FORMAT_UUID)
                    ->nullable()
            );
    }
}
