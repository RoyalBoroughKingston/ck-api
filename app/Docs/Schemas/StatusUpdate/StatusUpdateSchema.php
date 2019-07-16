<?php

namespace App\Docs\Schemas\StatusUpdate;

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
                    ->nullable()
                    ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b'),
                Schema::string('referral_id')
                    ->format(Schema::FORMAT_UUID)
                    ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b'),
                Schema::string('from')
                    ->enum('new', 'in_progress', 'completed', 'incompleted')
                    ->example('new'),
                Schema::string('to')
                    ->enum('new', 'in_progress', 'completed', 'incompleted')
                    ->example('new'),
                Schema::string('comments')
                    ->nullable()
                    ->example('Assigned to me'),
                Schema::string('created_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('updated_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable()
            );
    }
}
