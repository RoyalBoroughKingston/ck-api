<?php

namespace App\Docs\Paths\Taxonomies\Organisations;

use App\Docs\Operations\Taxonomies\Categories\ShowTaxonomyCategoryOperation;
use App\Docs\Operations\Taxonomies\Organisations\DestroyTaxonomyOrganisationOperation;
use App\Docs\Operations\Taxonomies\Organisations\UpdateTaxonomyOrganisationOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class TaxonomyOrganisationsNestedPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/taxonomies/organisations/{organisation}')
            ->operations(
                ShowTaxonomyCategoryOperation::create(),
                UpdateTaxonomyOrganisationOperation::create(),
                DestroyTaxonomyOrganisationOperation::create()
            );
    }
}
