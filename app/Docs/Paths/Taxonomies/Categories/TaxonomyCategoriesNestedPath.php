<?php

namespace App\Docs\Paths\Taxonomies\Categories;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class TaxonomyCategoriesNestedPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @return \GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/taxonomies/categories/{category}')
            ->operations(
                //
            );
    }
}
