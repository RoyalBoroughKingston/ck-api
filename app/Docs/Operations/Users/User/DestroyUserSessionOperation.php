<?php

namespace App\Docs\Operations\Users\User;

use App\Docs\Tags\UsersTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class DestroyUserSessionOperation extends Operation
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
            ->tags(UsersTag::create())
            ->summary("Clear the authenticated user's sessions")
            ->description('**Permission:** `Service Worker`')
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        Schema::object()->properties(
                            Schema::string('message')
                        )
                    )
                )
            );
    }
}
