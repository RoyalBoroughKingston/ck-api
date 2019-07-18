<?php

namespace App\Docs\Schemas\Audit;

use App\Models\Audit;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class AuditSchema extends Schema
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
                    ->format(Schema::FORMAT_UUID)
                    ->nullable(),
                Schema::string('oauth_client')
                    ->nullable(),
                Schema::string('action')
                    ->enum(
                        Audit::ACTION_CREATE,
                        Audit::ACTION_READ,
                        Audit::ACTION_UPDATE,
                        Audit::ACTION_DELETE
                    ),
                Schema::string('description'),
                Schema::string('ip_address'),
                Schema::string('user_agent')
                    ->nullable(),
                Schema::string('created_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('updated_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable()
            );
    }
}
