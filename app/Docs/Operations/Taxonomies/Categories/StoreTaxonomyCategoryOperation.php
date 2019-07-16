<?php

namespace App\Docs\Operations\Taxonomies\Categories;

use App\Docs\Schemas\ResourceSchema;
use App\Docs\Schemas\Taxonomy\Category\StoreTaxonomyCategorySchema;
use App\Docs\Schemas\Taxonomy\Category\TaxonomyCategorySchema;
use App\Docs\Tags\TaxonomyCategoriesTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class StoreTaxonomyCategoryOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->action(static::ACTION_POST)
            ->tags(TaxonomyCategoriesTag::create())
            ->summary('Create a category taxonomy')
            ->description('**Permission:** `Super Admin`')
            ->requestBody(
                RequestBody::create()
                    ->required()
                    ->content(
                        MediaType::json()->schema(StoreTaxonomyCategorySchema::create())
                    )
            )
            ->responses(
                Response::created()->content(
                    MediaType::json()->schema(
                        ResourceSchema::create(null, TaxonomyCategorySchema::create())
                    )
                )
            );
    }
}
