<?php

namespace App\Docs\Schemas\Audit;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class AuditSchema extends Schema
{
    /**
     * @param string|null $objectId
     * @return \GoldSpecDigital\ObjectOrientedOAS\Objects\Schema
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->type(static::TYPE_OBJECT)
            ->properties(
                Schema::string('id')
                    ->format(Schema::FORMAT_UUID)
                    ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b'),
                Schema::string('user_id')
                    ->format(Schema::FORMAT_UUID)
                    ->nullable()
                    ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b'),
                Schema::string('oauth_client')
                    ->nullable()
                    ->example('Connected Kingston Admin'),
                Schema::string('action')
                    ->enum('create', 'read', 'update', 'delete')
                    ->example('create'),
                Schema::string('description')
                    ->example('Created service [38e06e93-79b2-4c38-85bf-7749ebc7044b]'),
                Schema::string('ip_address')
                    ->example('192.168.0.1'),
                Schema::string('user_agent')
                    ->nullable()
                    ->example('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36'),
                Schema::string('created_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('updated_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable()
            );
    }
}
