<?php

namespace App\Docs\Paths\Taxonomies\Categories;

use App\Docs\Operations\Taxonomies\Categories\IndexTaxonomyCategoryOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class TaxonomyCategoriesIndexPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/taxonomies/categories/index')
            ->operations(
                IndexTaxonomyCategoryOperation::create()
                    ->action(IndexTaxonomyCategoryOperation::ACTION_POST)
                    ->description(
                        <<<'EOT'
This is an alias of `GET /taxonomies/categories` which allows all the query string parameters to be 
passed as part of the request body.
EOT
                    )
            );
    }
}
