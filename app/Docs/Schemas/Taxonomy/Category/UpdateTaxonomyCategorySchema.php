<?php

namespace App\Docs\Schemas\Taxonomy\Category;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateTaxonomyCategorySchema extends Schema
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
            ->required('parent_id', 'name', 'order')
            ->properties(
                Schema::string('parent_id')
                    ->format(Schema::FORMAT_UUID)
                    ->nullable()
                    ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b'),
                Schema::string('name')
                    ->example('Food Benefits'),
                Schema::integer('order')
                    ->example(1)
            );
    }
}
