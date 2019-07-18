<?php

namespace App\Docs\Operations\Taxonomies\Organisations;

use App\Docs\Parameters\PageParameter;
use App\Docs\Parameters\PerPageParameter;
use App\Docs\Schemas\PaginationSchema;
use App\Docs\Schemas\Taxonomy\Organisation\TaxonomyOrganisationSchema;
use App\Docs\Tags\TaxonomyOrganisationsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class IndexTaxonomyOrganisationOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->action(static::ACTION_GET)
            ->tags(TaxonomyOrganisationsTag::create())
            ->summary('List all the organisation taxonomies')
            ->description(
                <<<'EOT'
**Permission:** `Open`

---

Taxonomies are returned in ascending order of the order field.
EOT
            )
            ->noSecurity()
            ->parameters(
                PageParameter::create(),
                PerPageParameter::create()
            )
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        PaginationSchema::create(null, TaxonomyOrganisationSchema::create())
                    )
                )
            );
    }
}
