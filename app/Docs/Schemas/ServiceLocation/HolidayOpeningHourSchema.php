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
                Schema::boolean('is_closed')
                    ->example(true),
                Schema::string('starts_at')
                    ->format(Schema::FORMAT_DATE)
                    ->example('2018-12-24'),
                Schema::string('ends_at')
                    ->format(Schema::FORMAT_DATE)
                    ->example('2019-01-01'),
                Schema::string('opens_at')
                    ->format('time')
                    ->example('09:00:00'),
                Schema::string('closes_at')
                    ->format('time')
                    ->example('17:30:00')
            );
    }
}
