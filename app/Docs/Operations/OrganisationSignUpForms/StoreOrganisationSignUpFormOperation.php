<?php

namespace App\Docs\Operations\OrganisationSignUpForms;

use App\Docs\Responses\UpdateRequestReceivedResponse;
use App\Docs\Schemas\OrganisationSignUpForm\StoreOrganisationSignUpFormSchema;
use App\Docs\Tags\OrganisationSignUpFormsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;

class StoreOrganisationSignUpFormOperation extends Operation
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
            ->tags(OrganisationSignUpFormsTag::create())
            ->summary('Submit an organisation sign up form')
            ->description('**Permission:** `Open`')
            ->noSecurity()
            ->requestBody(
                RequestBody::create()
                    ->required()
                    ->content(
                        MediaType::json()->schema(
                            StoreOrganisationSignUpFormSchema::create()
                    )
                )
            )
            ->responses(
                UpdateRequestReceivedResponse::create(null, StoreOrganisationSignUpFormSchema::create())
            );
    }
}
