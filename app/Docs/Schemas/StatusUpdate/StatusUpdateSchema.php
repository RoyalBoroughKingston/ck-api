<?php

namespace App\Docs\Schemas\StatusUpdate;

use App\Models\StatusUpdate;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class StatusUpdateSchema extends Schema
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
                Schema::string('user_id')
                    ->format(Schema::FORMAT_UUID)
                    ->nullable(),
                Schema::string('referral_id')
                    ->format(Schema::FORMAT_UUID),
                Schema::string('from')
                    ->enum(
                        StatusUpdate::FROM_NEW,
                        StatusUpdate::FROM_IN_PROGRESS,
                        StatusUpdate::FROM_COMPLETED,
                        StatusUpdate::FROM_INCOMPLETED
                    ),
                Schema::string('to')
                    ->enum(
                        StatusUpdate::TO_NEW,
                        StatusUpdate::TO_IN_PROGRESS,
                        StatusUpdate::TO_COMPLETED,
                        StatusUpdate::TO_INCOMPLETED
                    ),
                Schema::string('comments')
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
