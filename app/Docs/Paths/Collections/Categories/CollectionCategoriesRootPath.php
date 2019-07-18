<?php

namespace App\Docs\Paths\Collections\Categories;

use App\Docs\Operations\Collections\Categories\IndexCollectionCategoryOperation;
use App\Docs\Operations\Collections\Categories\StoreCollectionCategoryOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class CollectionCategoriesRootPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/collections/categories')
            ->operations(
                IndexCollectionCategoryOperation::create(),
                StoreCollectionCategoryOperation::create()
            );
    }
}
