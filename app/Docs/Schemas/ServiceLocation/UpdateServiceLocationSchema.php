<?php

namespace App\Docs\Schemas\ServiceLocation;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateServiceLocationSchema extends Schema
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->type(static::TYPE_OBJECT)
            ->required('name', 'regular_opening_hours', 'holiday_opening_hours')
            ->properties(
                Schema::string('name'),
                Schema::array('regular_opening_hours')
                    ->items(
                        RegularOpeningHourSchema::create()
                            ->required('frequency', 'opens_at', 'closes_at')
                    ),
                Schema::array('holiday_opening_hours')
                    ->items(
                        HolidayOpeningHourSchema::create()->required(
                            'is_closed',
                            'starts_at',
                            'ends_at',
                            'opens_at',
                            'closes_at'
                        )
                    ),
                Schema::string('image_file_id')
                    ->format(Schema::FORMAT_UUID)
                    ->description('The ID of the file uploaded')
                    ->nullable()
            );
    }
}
