<?php

namespace App\Docs\Paths\Taxonomies\Organisations;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class TaxonomyOrganisationsNestedPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/taxonomies/organisations/{organisation}')
            ->operations(
                //
            );
    }
}
