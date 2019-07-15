<?php

namespace App\Docs\Schemas\Collection\Persona;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateCollectionPersonaSchema extends Schema
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
                'name',
                'intro',
                'subtitle',
                'order',
                'sideboxes',
                'category_taxonomies'
            )
            ->properties(
                Schema::string('name')
                    ->example('Refugees'),
                Schema::string('intro')
                    ->example('Lorem ipsum'),
                Schema::string('subtitle')
                    ->example('Lorem ipsum'),
                Schema::integer('order')
                    ->example(1),
                Schema::array('sideboxes')
                    ->maxItems(3)
                    ->items(
                        Schema::object()
                            ->required('title', 'content')
                            ->properties(
                                Schema::string('title')
                                    ->example('Lorem ipsum'),
                                Schema::string('content')
                                    ->example('Lorem ipsum dolar sit amet')
                            )
                    ),
                Schema::array('category_taxonomies')
                    ->items(
                        Schema::string()
                            ->format(Schema::FORMAT_UUID)
                            ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b')
                    ),
                Schema::string('image_file_id')
                    ->format(Schema::FORMAT_UUID)
                    ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b')
                    ->description('The ID of the file uploaded')
                    ->nullable()
            );
    }
}
