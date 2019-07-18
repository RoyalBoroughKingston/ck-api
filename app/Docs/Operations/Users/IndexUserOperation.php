<?php

namespace App\Docs\Operations\Users;

use App\Docs\Parameters\FilterIdParameter;
use App\Docs\Parameters\FilterParameter;
use App\Docs\Parameters\IncludeParameter;
use App\Docs\Parameters\PageParameter;
use App\Docs\Parameters\PerPageParameter;
use App\Docs\Parameters\SortParameter;
use App\Docs\Schemas\PaginationSchema;
use App\Docs\Schemas\User\UserSchema;
use App\Docs\Tags\UsersTag;
use App\Models\Role;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class IndexUserOperation extends Operation
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
            ->summary('List all the users')
            ->description('**Permission:** `Service Worker`')
            ->parameters(
                PageParameter::create(),
                PerPageParameter::create(),
                FilterIdParameter::create(),
                FilterParameter::create(null, 'first_name')
                    ->description('First name to filter by')
                    ->schema(Schema::string()),
                FilterParameter::create(null, 'last_name')
                    ->description('Last name to filter by')
                    ->schema(Schema::string()),
                FilterParameter::create(null, 'email')
                    ->description('Email to filter by')
                    ->schema(Schema::string()),
                FilterParameter::create(null, 'phone')
                    ->description('Phone to filter by')
                    ->schema(Schema::string()),
                FilterParameter::create(null, 'highest_role')
                    ->description('Comma separated list of highest role to filter by')
                    ->schema(
                        Schema::array()->items(
                            Schema::string()->enum(
                                ...Role::query()->pluck('name')->toArray()
                            )
                        )
                    )
                    ->style(FilterParameter::STYLE_SIMPLE),
                FilterParameter::create('has_permission')
                    ->description('Filter users to only ones they have permissions for')
                    ->schema(Schema::boolean()),
                FilterParameter::create(null, 'at_organisation')
                    ->description('Comma separated list of organisation IDs which filters users to those who have a role at the specified organisations (global admins and higher are excluded)')
                    ->schema(Schema::string()),
                FilterParameter::create(null, 'at_service')
                    ->description('Comma separated list of service IDs which filters users to those who have a role at the specified services (global admins and higher are excluded)')
                    ->schema(Schema::string()),
                IncludeParameter::create(null, [
                    'user-roles',
                    'user-roles.organisation',
                    'user-roles.service',
                ]),
                SortParameter::create(null, [
                    'first_name',
                    'last_name',
                    'highest_role',
                ], 'first_name,last_name')
            )
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        PaginationSchema::create(null, UserSchema::create())
                    )
                )
            );
    }
}
