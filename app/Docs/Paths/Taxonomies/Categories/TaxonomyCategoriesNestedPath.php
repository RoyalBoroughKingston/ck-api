<?php

namespace App\Docs\Paths\Taxonomies\Categories;

use App\Docs\Operations\Taxonomies\Categories\DestroyTaxonomyCategoryOperation;
use App\Docs\Operations\Taxonomies\Categories\ShowTaxonomyCategoryOperation;
use App\Docs\Operations\Taxonomies\Categories\UpdateTaxonomyCategoryOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

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
            ->parameters(
                Parameter::path()
                    ->name('category')
                    ->description('The ID of the category taxonomy')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID))
            )
            ->operations(
                ShowTaxonomyCategoryOperation::create(),
                UpdateTaxonomyCategoryOperation::create(),
                DestroyTaxonomyCategoryOperation::create()
            );
    }
}
