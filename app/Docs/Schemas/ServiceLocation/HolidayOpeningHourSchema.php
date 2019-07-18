<?php

namespace App\Docs\Schemas\ServiceLocation;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class HolidayOpeningHourSchema extends Schema
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
                Schema::boolean('is_closed'),
                Schema::string('starts_at')
                    ->format(Schema::FORMAT_DATE),
                Schema::string('ends_at')
                    ->format(Schema::FORMAT_DATE),
                Schema::string('opens_at')
                    ->format('time'),
                Schema::string('closes_at')
                    ->format('time')
            );
    }
}
