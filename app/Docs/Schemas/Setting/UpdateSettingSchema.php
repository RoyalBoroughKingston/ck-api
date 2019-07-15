<?php

namespace App\Docs\Schemas\Setting;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateSettingSchema extends SettingSchema
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        $instance = parent::create($objectId);

        $properties = array_map(function (Schema $schema): Schema {
            return static::requireAllProperties($schema);
        }, $instance->properties);

        $instance = $instance
            ->required(...$instance->properties)
            ->properties(...$properties);

        return $instance;
    }

    /**
     * @param \GoldSpecDigital\ObjectOrientedOAS\Objects\Schema $schema
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return \GoldSpecDigital\ObjectOrientedOAS\Objects\Schema
     */
    protected static function requireAllProperties(Schema $schema): Schema
    {
        // Only attempt to require properties for objects.
        if ($schema->type === Schema::TYPE_OBJECT) {
            // Loop through the properties of the objest.
            foreach ($schema->properties as $property) {
                // If the property is itself an object, then use recursion.
                if ($property->type === Schema::TYPE_OBJECT) {
                    $property = static::requireAllProperties($property);
                }

                // Add the current property to the list of required properties.
                $schema = $schema->required($property, ...$schema->required);
            }
        }

        return $schema;
    }
}
