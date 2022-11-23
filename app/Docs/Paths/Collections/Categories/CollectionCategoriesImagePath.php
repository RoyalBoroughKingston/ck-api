<?php

namespace App\Docs\Paths\Collections\Categories;

use App\Docs\Operations\Collections\Categories\ImageCollectionCategoryOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class CollectionCategoriesImagePath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/collections/categories/{category}/image.svg')
            ->parameters(
                Parameter::path()
                    ->name('category')
                    ->description('The ID of the category collection')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID))
            )
            ->operations(
                ImageCollectionCategoryOperation::create()
            );
    }
}
