<?php

namespace App\Docs\Paths\Collections\Categories;

use App\Docs\Operations\Collections\Categories\DestroyCollectionCategoryOperation;
use App\Docs\Operations\Collections\Categories\ShowCollectionCategoryOperation;
use App\Docs\Operations\Collections\Categories\UpdateCollectionCategoryOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class CollectionCategoriesNestedPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/collections/categories/{category}')
            ->operations(
                ShowCollectionCategoryOperation::create(),
                UpdateCollectionCategoryOperation::create(),
                DestroyCollectionCategoryOperation::create()
            );
    }
}
