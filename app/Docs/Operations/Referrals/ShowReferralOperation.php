<?php

namespace App\Docs\Operations\Referrals;

use App\Docs\Parameters\IncludeParameter;
use App\Docs\Schemas\Referral\ReferralSchema;
use App\Docs\Schemas\ResourceSchema;
use App\Docs\Tags\ReferralsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ShowReferralOperation extends Operation
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
            ->tags(ReferralsTag::create())
            ->summary('Get a specific referral')
            ->description('**Permission:** `Service Worker`')
            ->parameters(
                Parameter::path()
                    ->name('referral')
                    ->description('The ID of the referral')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID)),
                IncludeParameter::create(null, ['service'])
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
