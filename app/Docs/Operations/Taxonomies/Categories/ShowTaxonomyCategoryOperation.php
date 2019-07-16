<?php

namespace App\Docs\Operations\Taxonomies\Categories;

use App\Docs\Schemas\ResourceSchema;
use App\Docs\Schemas\Taxonomy\Category\TaxonomyCategorySchema;
use App\Docs\Tags\TaxonomyCategoriesTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

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
            ->parameters(
                Parameter::path()
                    ->name('category')
                    ->description('The ID of the category taxonomy')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID))
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
