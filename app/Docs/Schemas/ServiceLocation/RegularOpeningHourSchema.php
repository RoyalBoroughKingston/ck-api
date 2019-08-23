<?php

namespace App\Docs\Schemas\ServiceLocation;

use App\Models\RegularOpeningHour;
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
                    ->enum(
                        RegularOpeningHour::FREQUENCY_WEEKLY,
                        RegularOpeningHour::FREQUENCY_MONTHLY,
                        RegularOpeningHour::FREQUENCY_FORTNIGHTLY,
                        RegularOpeningHour::FREQUENCY_NTH_OCCURRENCE_OF_MONTH
                    ),
                Schema::integer('weekday'),
                Schema::integer('day_of_month'),
                Schema::integer('occurrence_of_month'),
                Schema::string('starts_at')
                    ->format(Schema::FORMAT_DATE),
                Schema::string('opens_at')
                    ->format('time'),
                Schema::string('closes_at')
                    ->format('time')
            );
    }
}
