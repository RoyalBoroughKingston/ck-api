<?php

namespace App\Docs\Operations\Users;

use App\Docs\Responses\ResourceDeletedResponse;
use App\Docs\Tags\UsersTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class DestroyUserOperation extends Operation
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
            ->summary('Delete a specific user')
            ->description(
                <<<'EOT'
**Permission:** `Service Admin`
- Can delete service workers
- Can delete other service admins

**Permission:** `Global Admin`
- Can delete other global admins

**Permission:** `Super Admin`
- Can delete other super admins
EOT
            )
            ->parameters(
                Parameter::path()
                    ->name('user')
                    ->description('The ID of the user')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID))
            )
            ->responses(ResourceDeletedResponse::create(null, 'user'));
    }
}
