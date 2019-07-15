<?php

namespace App\Docs\Schemas\User;

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
                        'Service Worker',
                        'Service Admin',
                        'Organisation Admin',
                        'Global Admin',
                        'Super Admin'
                    )
                    ->example('Service Worker'),
                Schema::string('organisation_id')
                    ->format(Schema::FORMAT_UUID)
                    ->nullable()
                    ->example(null),
                Schema::string('service_id')
                    ->format(Schema::FORMAT_UUID)
                    ->nullable()
                    ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b')
            );
    }
}
