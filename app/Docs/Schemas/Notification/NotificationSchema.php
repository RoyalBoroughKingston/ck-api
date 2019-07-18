<?php

namespace App\Docs\Schemas\Notification;

use App\Models\Notification;
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
                Schema::string('id'),
                Schema::string('notifiable_type')
                    ->nullable(),
                Schema::string('notifiable_id')
                    ->format(Schema::FORMAT_UUID)
                    ->nullable(),
                Schema::string('channel')
                    ->enum(Notification::CHANNEL_EMAIL, Notification::CHANNEL_SMS),
                Schema::string('recipient'),
                Schema::string('message'),
                Schema::string('sent_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('failed_at')
                    ->format(Schema::FORMAT_DATE_TIME)
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
