<?php

namespace App\Docs\Schemas\UpdateRequest;

use App\Models\UpdateRequest;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateRequestSchema extends Schema
{
    /**
     * @param string|null $objectId
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->type(static::TYPE_OBJECT)
            ->properties(
                Schema::string('id')
                    ->format(Schema::FORMAT_UUID),
                Schema::string('user_id')
                    ->format(Schema::FORMAT_UUID)
                    ->nullable(),
                Schema::string('actioning_user_id')
                    ->format(Schema::FORMAT_UUID)
                    ->nullable(),
                Schema::string('updateable_type')
                    ->enum(
                        UpdateRequest::EXISTING_TYPE_LOCATION,
                        UpdateRequest::EXISTING_TYPE_REFERRAL,
                        UpdateRequest::EXISTING_TYPE_SERVICE,
                        UpdateRequest::EXISTING_TYPE_SERVICE_LOCATION,
                        UpdateRequest::EXISTING_TYPE_ORGANISATION,
                        UpdateRequest::EXISTING_TYPE_USER,
                        UpdateRequest::NEW_TYPE_ORGANISATION_SIGN_UP_FORM
                    ),
                Schema::string('updateable_id')
                    ->format(Schema::FORMAT_UUID)
                    ->nullable(),
                Schema::string('entry'),
                Schema::object('data'),
                Schema::string('created_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('updated_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('approved_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('deleted_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable()
            );
    }
}
