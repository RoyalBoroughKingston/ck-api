<?php

namespace App\Docs\Paths\Taxonomies\Categories;

use App\Docs\Operations\Taxonomies\Categories\IndexTaxonomyCategoryOperation;
use App\Docs\Operations\Taxonomies\Categories\StoreTaxonomyCategoryOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class TaxonomyCategoriesRootPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/taxonomies/categories')
            ->operations(
                IndexTaxonomyCategoryOperation::create(),
                StoreTaxonomyCategoryOperation::create()
            );
    }
}
