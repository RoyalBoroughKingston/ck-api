<?php

namespace App\Docs\Paths\Taxonomies\Organisations;

use App\Docs\Operations\Taxonomies\Organisations\DestroyTaxonomyOrganisationOperation;
use App\Docs\Operations\Taxonomies\Organisations\ShowTaxonomyOrganisationOperation;
use App\Docs\Operations\Taxonomies\Organisations\UpdateTaxonomyOrganisationOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

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
            ->parameters(
                Parameter::path()
                    ->name('organisation')
                    ->description('The ID of the organisation taxonomy')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID))
            )
            ->operations(
                ShowTaxonomyOrganisationOperation::create(),
                UpdateTaxonomyOrganisationOperation::create(),
                DestroyTaxonomyOrganisationOperation::create()
            );
    }
}
