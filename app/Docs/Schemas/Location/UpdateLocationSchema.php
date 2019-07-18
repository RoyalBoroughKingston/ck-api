<?php

namespace App\Docs\Schemas\Location;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateLocationSchema extends Schema
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
            ->required(
                'address_line_1',
                'address_line_2',
                'address_line_3',
                'city',
                'county',
                'postcode',
                'country',
                'accessibility_info',
                'has_wheelchair_access',
                'has_induction_loop'
            )
            ->properties(
                Schema::string('address_line_1'),
                Schema::string('address_line_2')
                    ->nullable(),
                Schema::string('address_line_3')
                    ->nullable(),
                Schema::string('city'),
                Schema::string('county'),
                Schema::string('postcode'),
                Schema::string('country'),
                Schema::string('accessibility_info')
                    ->nullable(),
                Schema::boolean('has_wheelchair_access'),
                Schema::boolean('has_induction_loop'),
                Schema::string('image_file_id')
                    ->format(Schema::FORMAT_UUID)
                    ->description('The ID of the file uploaded')
                    ->nullable()
            );
    }
}
