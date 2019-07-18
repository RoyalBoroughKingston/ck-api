<?php

namespace App\Docs\Operations\Taxonomies\Categories;

use App\Docs\Schemas\ResourceSchema;
use App\Docs\Schemas\Taxonomy\Category\TaxonomyCategorySchema;
use App\Docs\Tags\TaxonomyCategoriesTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class ShowTaxonomyCategoryOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->action(static::ACTION_GET)
            ->tags(TaxonomyCategoriesTag::create())
            ->summary('Get a specific category taxonomy')
            ->description('**Permission:** `Open`')
            ->noSecurity()
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        ResourceSchema::create(null, TaxonomyCategorySchema::create())
                    )
                )
            );
    }
}
