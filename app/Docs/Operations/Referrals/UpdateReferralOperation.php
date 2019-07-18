<?php

namespace App\Docs\Operations\Referrals;

use App\Docs\Schemas\Referral\ReferralSchema;
use App\Docs\Schemas\Referral\UpdateReferralSchema;
use App\Docs\Schemas\ResourceSchema;
use App\Docs\Tags\ReferralsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class UpdateReferralOperation extends Operation
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
            ->tags(ReferralsTag::create())
            ->summary('Update a specific referral')
            ->description('**Permission:** `Service Worker`')
            ->requestBody(
                RequestBody::create()
                    ->required()
                    ->content(
                        MediaType::json()->schema(UpdateReferralSchema::create())
                    )
            )
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        ResourceSchema::create(null, ReferralSchema::create())
                    )
                )
            );
    }
}
