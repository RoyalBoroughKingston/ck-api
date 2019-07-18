<?php

namespace App\Docs\Paths\Taxonomies\Organisations;

use App\Docs\Operations\Taxonomies\Organisations\IndexTaxonomyOrganisationOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class TaxonomyOrganisationsIndexPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/taxonomies/organisations/index')
            ->operations(
                IndexTaxonomyOrganisationOperation::create()
                    ->action(IndexTaxonomyOrganisationOperation::ACTION_POST)
                    ->description(
                        <<<'EOT'
This is an alias of `GET /taxonomies/organisations` which allows all the query string parameters to 
be passed as part of the request body.
EOT
                    )
            );
    }
}
