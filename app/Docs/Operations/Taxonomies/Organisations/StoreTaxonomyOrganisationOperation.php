<?php

namespace App\Docs\Operations\Taxonomies\Organisations;

use App\Docs\Schemas\ResourceSchema;
use App\Docs\Schemas\Taxonomy\Organisation\StoreTaxonomyOrganisationSchema;
use App\Docs\Schemas\Taxonomy\Organisation\TaxonomyOrganisationSchema;
use App\Docs\Tags\TaxonomyOrganisationsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class StoreTaxonomyOrganisationOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->action(static::ACTION_POST)
            ->tags(TaxonomyOrganisationsTag::create())
            ->summary('Create a organisation taxonomy')
            ->description('**Permission:** `Super Admin`')
            ->requestBody(
                RequestBody::create()
                    ->required()
                    ->content(
                        MediaType::json()->schema(StoreTaxonomyOrganisationSchema::create())
                    )
            )
            ->responses(
                Response::created()->content(
                    MediaType::json()->schema(
                        ResourceSchema::create(null, TaxonomyOrganisationSchema::create())
                    )
                )
            );
    }
}
