<?php

namespace App\Docs\Operations\Users;

use App\Docs\Schemas\ResourceSchema;
use App\Docs\Schemas\User\StoreUserSchema;
use App\Docs\Schemas\User\UserSchema;
use App\Docs\Tags\UsersTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class StoreUserOperation extends Operation
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
            ->tags(UsersTag::create())
            ->summary('Create a user')
            ->description(
                <<<'EOT'
**Permission:** `Service Admin`
- Can create service workers
- Can create other service admins

**Permission:** `Global Admin`
- Can create other global admins

**Permission:** `Super Admin`
- Can create other super admins
EOT
            )
            ->requestBody(
                RequestBody::create()
                    ->required()
                    ->content(
                        MediaType::json()->schema(StoreUserSchema::create())
                    )
            )
            ->responses(
                Response::created()->content(
                    MediaType::json()->schema(
                        ResourceSchema::create(null, UserSchema::create())
                    )
                )
            );
    }
}
