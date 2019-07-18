<?php

namespace App\Docs\Operations\Taxonomies\Organisations;

use App\Docs\Schemas\ResourceSchema;
use App\Docs\Schemas\Taxonomy\Organisation\TaxonomyOrganisationSchema;
use App\Docs\Tags\TaxonomyOrganisationsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class ShowTaxonomyOrganisationOperation extends Operation
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
            ->summary('Get a specific organisation taxonomy')
            ->description('**Permission:** `Open`')
            ->noSecurity()
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        ResourceSchema::create(null, TaxonomyOrganisationSchema::create())
                    )
                )
            );
    }
}
