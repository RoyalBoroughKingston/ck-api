<?php

namespace App\Docs\Operations\Users;

use App\Docs\Parameters\IncludeParameter;
use App\Docs\Schemas\ResourceSchema;
use App\Docs\Schemas\User\UserSchema;
use App\Docs\Tags\UsersTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ShowUserOperation extends Operation
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
            ->tags(UsersTag::create())
            ->summary('Get a specific user')
            ->description('**Permission:** `Service Worker`')
            ->parameters(
                Parameter::path()
                    ->name('user')
                    ->description('The ID of the user')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID)),
                IncludeParameter::create(null, [
                    'user-roles',
                    'user-roles.organisation',
                    'user-roles.service',
                ])
            )
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        ResourceSchema::create(null, UserSchema::create())
                    )
                )
            );
    }
}
