<?php

namespace App\Docs\Schemas\Collection\Category;

use App\Docs\Schemas\Taxonomy\Category\TaxonomyCategorySchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class CollectionCategorySchema extends Schema
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
                Schema::string('name')
                    ->example('Leisure and Social Activities'),
                Schema::string('intro')
                    ->example('Lorem ipsum'),
                Schema::string('icon')
                    ->example('coffee'),
                Schema::integer('order')
                    ->example(1),
                Schema::array('sideboxes')
                    ->maxItems(3)
                    ->items(
                        Schema::object()->properties(
                            Schema::string('title')
                                ->example('Lorem ipsum'),
                            Schema::string('content')
                                ->example('Lorem ipsum dolar sit amet')
                        )
                    ),
                Schema::array('category_taxonomies')
                    ->items(TaxonomyCategorySchema::create()),
                Schema::string('created_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('updated_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable()
            );
    }
}
