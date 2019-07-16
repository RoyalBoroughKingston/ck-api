<?php

namespace App\Docs\Operations\Organisations;

use App\Docs\Responses\ResourceDeletedResponse;
use App\Docs\Schemas\Organisation\UpdateOrganisationSchema;
use App\Docs\Tags\OrganisationsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class DestroyOrganisationOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->action(static::ACTION_DELETE)
            ->tags(OrganisationsTag::create())
            ->summary('Delete a specific organisation')
            ->description('**Permission:** `Super Admin`')
            ->parameters(
                Parameter::path()
                    ->name('organisation')
                    ->description('The ID or slug of the organisation')
                    ->required()
                    ->schema(Schema::string())
            )
            ->requestBody(
                RequestBody::create()
                    ->required()
                    ->content(
                        MediaType::json()->schema(UpdateOrganisationSchema::create())
                    )
            )
            ->responses(
                ResourceDeletedResponse::create(null, 'organisation')
            );
    }
}
