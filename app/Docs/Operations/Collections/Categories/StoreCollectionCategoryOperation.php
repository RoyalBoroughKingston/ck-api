<?php

namespace App\Docs\Operations\Collections\Categories;

use App\Docs\Schemas\Collection\Category\CollectionCategorySchema;
use App\Docs\Schemas\Collection\Category\StoreCollectionCategorySchema;
use App\Docs\Schemas\ResourceSchema;
use App\Docs\Tags\CollectionCategoriesTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class StoreCollectionCategoryOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return \GoldSpecDigital\ObjectOrientedOAS\Objects\Operation
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->action(static::ACTION_POST)
            ->tags(CollectionCategoriesTag::create())
            ->summary('Create a category collection')
            ->description('**Permission:** `Super Admin`')
            ->requestBody(
                RequestBody::create()
                    ->required()
                    ->content(
                        MediaType::json()->schema(
                            StoreCollectionCategorySchema::create()
                        )
                    )
            )
            ->responses(
                Response::created()->content(
                    MediaType::json()->schema(
                        ResourceSchema::create(null, CollectionCategorySchema::create())
                    )
                )
            );
    }
}
