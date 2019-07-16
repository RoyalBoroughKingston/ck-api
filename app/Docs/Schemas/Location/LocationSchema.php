<?php

namespace App\Docs\Schemas\Location;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class LocationSchema extends Schema
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
                    ->format(Schema::FORMAT_UUID)
                    ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b'),
                Schema::boolean('has_image'),
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
                Schema::string('county')
                    ->example('West Yorkshire'),
                Schema::string('postcode')
                    ->example('LS1 4HT'),
                Schema::string('country')
                    ->example('United Kingdom'),
                Schema::number('lat')
                    ->format(Schema::FORMAT_FLOAT)
                    ->example(5.78263),
                Schema::number('lon')
                    ->format(Schema::FORMAT_FLOAT)
                    ->example(-52.12710),
                Schema::string('accessibility_info')
                    ->nullable()
                    ->example(null),
                Schema::boolean('has_wheelchair_access'),
                Schema::boolean('has_induction_loop'),
                Schema::string('created_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('updated_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable()
            );
    }
}
