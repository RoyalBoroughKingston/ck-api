<?php

namespace App\Docs\Schemas\Notification;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class NotificationSchema extends Schema
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
                    ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b'),
                Schema::string('notifiable_type')
                    ->nullable()
                    ->example('referrals'),
                Schema::string('notifiable_id')
                    ->format(Schema::FORMAT_UUID)
                    ->nullable()
                    ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b'),
                Schema::string('channel')
                    ->enum('email', 'sms')
                    ->example('email'),
                Schema::string('recipient')
                    ->example('john.doe@example.com'),
                Schema::string('message')
                    ->example('Lorem ipsum'),
                Schema::string('sent_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('failed_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable()
                    ->example(null),
                Schema::string('created_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('updated_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable()
            );
    }
}
