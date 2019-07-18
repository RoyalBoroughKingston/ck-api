<?php

namespace App\Docs\Schemas\ServiceLocation;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class StoreServiceLocationSchema extends UpdateServiceLocationSchema
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        $instance = parent::create($objectId);

        $instance = $instance
            ->required('service_id', 'location_id', ...$instance->required)
            ->properties(
                Schema::string('service_id')
                    ->format(Schema::FORMAT_UUID),
                Schema::string('location_id')
                    ->format(Schema::FORMAT_UUID),
                ...$instance->properties
            );

        return $instance;
    }
}
