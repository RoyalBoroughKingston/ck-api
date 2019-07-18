<?php

namespace App\Docs\Schemas\ServiceLocation;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ServiceLocationSchema extends Schema
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
                Schema::string('service_id')
                    ->format(Schema::FORMAT_UUID),
                Schema::string('location_id')
                    ->format(Schema::FORMAT_UUID),
                Schema::boolean('has_image'),
                Schema::string('name'),
                Schema::boolean('is_open_now')
                    ->nullable(true),
                Schema::array('regular_opening_hours')
                    ->items(RegularOpeningHourSchema::create()),
                Schema::array('holiday_opening_hours')
                    ->items(HolidayOpeningHourSchema::create()),
                Schema::string('created_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('updated_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable()
            );
    }
}
