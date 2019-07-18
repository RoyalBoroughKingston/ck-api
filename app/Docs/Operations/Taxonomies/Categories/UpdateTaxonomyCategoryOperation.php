<?php

namespace App\Docs\Operations\Taxonomies\Categories;

use App\Docs\Schemas\ResourceSchema;
use App\Docs\Schemas\Taxonomy\Category\TaxonomyCategorySchema;
use App\Docs\Schemas\Taxonomy\Category\UpdateTaxonomyCategorySchema;
use App\Docs\Tags\TaxonomyCategoriesTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class UpdateTaxonomyCategoryOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->action(static::ACTION_PUT)
            ->tags(TaxonomyCategoriesTag::create())
            ->summary('Update a specific category taxonomy')
            ->description('**Permission:** `Global Admin`')
            ->requestBody(
                RequestBody::create()
                    ->required()
                    ->content(
                        MediaType::json()->schema(UpdateTaxonomyCategorySchema::create())
                    )
            )
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        ResourceSchema::create(null, TaxonomyCategorySchema::create())
                    )
                )
            );
    }
}
