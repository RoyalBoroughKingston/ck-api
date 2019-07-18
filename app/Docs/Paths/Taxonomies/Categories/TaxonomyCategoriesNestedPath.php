<?php

namespace App\Docs\Paths\Taxonomies\Categories;

use App\Docs\Operations\Taxonomies\Categories\DestroyTaxonomyCategoryOperation;
use App\Docs\Operations\Taxonomies\Categories\ShowTaxonomyCategoryOperation;
use App\Docs\Operations\Taxonomies\Categories\UpdateTaxonomyCategoryOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class TaxonomyCategoriesNestedPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/taxonomies/categories/{category}')
            ->operations(
                ShowTaxonomyCategoryOperation::create(),
                UpdateTaxonomyCategoryOperation::create(),
                DestroyTaxonomyCategoryOperation::create()
            );
    }
}
