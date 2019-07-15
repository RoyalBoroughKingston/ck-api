<?php

namespace App\Docs\Schemas\ServiceLocation;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class RegularOpeningHourSchema extends Schema
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
                Schema::string('frequency')
                    ->enum('weekly', 'monthly', 'fortnightly', 'nth_occurrence_of_month')
                    ->example('weekly'),
                Schema::integer('weekday')
                    ->example(2),
                Schema::integer('day_of_month')
                    ->example(null),
                Schema::integer('occurrence_of_month')
                    ->example(null),
                Schema::string('starts_at')
                    ->format(Schema::FORMAT_DATE)
                    ->example(null),
                Schema::string('opens_at')
                    ->format('time')
                    ->example('09:00:00'),
                Schema::string('closes_at')
                    ->format('time')
                    ->example('17:30:00')
            );
    }
}
