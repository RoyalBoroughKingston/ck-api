<?php

namespace App\Docs\Operations\Organisations;

use App\Docs\Schemas\Organisation\OrganisationSchema;
use App\Docs\Schemas\ResourceSchema;
use App\Docs\Tags\OrganisationsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class ShowOrganisationOperation extends Operation
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
            ->tags(OrganisationsTag::create())
            ->summary('Get a specific organisation')
            ->description('**Permission:** `Open`')
            ->noSecurity()
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        ResourceSchema::create(null, OrganisationSchema::create())
                    )
                )
            );
    }
}
