<?php

namespace App\Docs\Paths\Taxonomies\Organisations;

use App\Docs\Operations\Taxonomies\Organisations\IndexTaxonomyOrganisationOperation;
use App\Docs\Operations\Taxonomies\Organisations\StoreTaxonomyOrganisationOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class TaxonomyOrganisationsRootPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/taxonomies/organisations')
            ->operations(
                IndexTaxonomyOrganisationOperation::create(),
                StoreTaxonomyOrganisationOperation::create()
            );
    }
}
