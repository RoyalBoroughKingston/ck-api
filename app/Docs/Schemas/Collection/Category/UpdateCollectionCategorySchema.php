<?php

namespace App\Docs\Schemas\Collection\Category;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateCollectionCategorySchema extends Schema
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
                'slug',
                'name',
                'intro',
                'image_file_id',
                'order',
                'sideboxes',
                'category_taxonomies'
            )
            ->properties(
                Schema::string('slug'),
                Schema::string('name'),
                Schema::string('intro'),
                Schema::integer('order'),
                Schema::boolean('homepage'),
                Schema::array('sideboxes')
                    ->maxItems(3)
                    ->items(
                        Schema::object()
                            ->required('title', 'content')
                            ->properties(
                                Schema::string('title'),
                                Schema::string('content')
                            )
                    ),
                Schema::string('image_file_id')
                    ->format(Schema::FORMAT_UUID)
                    ->description('The ID of the file uploaded')
                    ->nullable(),
                Schema::array('category_taxonomies')
                    ->items(
                        Schema::string()
                            ->format(Schema::FORMAT_UUID)
                    )
            );
    }
}
