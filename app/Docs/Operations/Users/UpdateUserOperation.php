<?php

namespace App\Docs\Operations\Users;

use App\Docs\Schemas\ResourceSchema;
use App\Docs\Schemas\User\UpdateUserSchema;
use App\Docs\Schemas\User\UserSchema;
use App\Docs\Tags\UsersTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class UpdateUserOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->action(static::ACTION_PUT)
            ->tags(UsersTag::create())
            ->summary('Update a specific user')
            ->description(
                <<<'EOT'
**Permission:** `Service Admin`
- Can update service workers
- Can update other service admins

**Permission:** `Global Admin`
- Can update other global admins

**Permission:** `Super Admin`
- Can update other super admins
EOT
            )
            ->requestBody(
                RequestBody::create()
                    ->required()
                    ->content(
                        MediaType::json()->schema(UpdateUserSchema::create())
                    )
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
