<?php

namespace App\Docs\Operations\Referrals;

use App\Docs\Responses\ResourceDeletedResponse;
use App\Docs\Tags\ReferralsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

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
            ->parameters(
                Parameter::path()
                    ->name('referral')
                    ->description('The ID of the referral')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID))
            )
            ->responses(ResourceDeletedResponse::create());
    }
}
