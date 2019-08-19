<?php

namespace App\Docs\Schemas\UpdateRequest;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateRequestSchema extends Schema
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
                    ->format(Schema::FORMAT_UUID),
                Schema::string('user_id')
                    ->format(Schema::FORMAT_UUID),
                Schema::string('updateable_type')
                    ->enum(
                        'locations',
                        'referrals',
                        'services',
                        'service_locations',
                        'organisations',
                        'users'
                    ),
                Schema::string('updateable_id')
                    ->format(Schema::FORMAT_UUID),
                Schema::string('entry'),
                Schema::object('data'),
                Schema::string('created_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('updated_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('approved_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('deleted_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable()
            );
    }
}
