<?php

namespace App\Docs\Paths\Collections\Categories;

use App\Docs\Operations\Collections\Categories\IndexCollectionCategoryOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class CollectionCategoriesIndexPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/collections/categories/index')
            ->operations(
                IndexCollectionCategoryOperation::create()
                    ->action(IndexCollectionCategoryOperation::ACTION_POST)
            );
    }
}
