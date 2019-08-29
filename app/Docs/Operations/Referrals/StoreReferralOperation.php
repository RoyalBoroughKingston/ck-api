<?php

namespace App\Docs\Operations\Referrals;

use App\Docs\Schemas\Referral\ReferralSchema;
use App\Docs\Schemas\Referral\StoreReferralSchema;
use App\Docs\Schemas\ResourceSchema;
use App\Docs\Tags\ReferralsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class StoreReferralOperation extends Operation
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
            ->tags(ReferralsTag::create())
            ->summary('Create a referral')
            ->description('**Permission:** `Open`')
            ->noSecurity()
            ->requestBody(
                RequestBody::create()
                    ->required()
                    ->content(
                        MediaType::json()->schema(StoreReferralSchema::create())
                    )
            )
            ->responses(
                Response::created()->content(
                    MediaType::json()->schema(
                        ResourceSchema::create(null, ReferralSchema::create())
                    )
                )
            );
    }
}
