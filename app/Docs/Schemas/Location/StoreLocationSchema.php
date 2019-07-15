<?php

namespace App\Docs\Schemas\Location;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class StoreLocationSchema extends Schema
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
                Schema::string('address_line_1')
                    ->example('30-34 Aire St'),
                Schema::string('address_line_2')
                    ->nullable()
                    ->example(null),
                Schema::string('address_line_3')
                    ->nullable()
                    ->example(null),
                Schema::string('city')
                    ->example('Leeds'),
                Schema::string('postcode')
                    ->example('LS1 4HT'),
                Schema::string('country')
                    ->example('United Kingdom'),
                Schema::string('accessibility_info')
                    ->nullable()
                    ->example(null),
                Schema::boolean('has_wheelchair_access'),
                Schema::boolean('has_induction_loop'),
                Schema::string('image_file_id')
                    ->format(Schema::FORMAT_UUID)
                    ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b')
                    ->description('The ID of the file uploaded')
                    ->nullable()
            );
    }
}
