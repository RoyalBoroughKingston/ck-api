<?php

namespace App\Docs\Operations\Referrals;

use App\Docs\Responses\ResourceDeletedResponse;
use App\Docs\Tags\ReferralsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;

class DestroyReferralOperation extends Operation
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
            ->tags(ReferralsTag::create())
            ->summary('Delete a specific referral')
            ->description('**Permission:** `Super Worker`')
            ->responses(ResourceDeletedResponse::create());
    }
}
