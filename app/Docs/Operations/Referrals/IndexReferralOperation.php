<?php

namespace App\Docs\Operations\Referrals;

use App\Docs\Parameters\FilterIdParameter;
use App\Docs\Parameters\FilterParameter;
use App\Docs\Parameters\IncludeParameter;
use App\Docs\Parameters\PageParameter;
use App\Docs\Parameters\PerPageParameter;
use App\Docs\Parameters\SortParameter;
use App\Docs\Schemas\PaginationSchema;
use App\Docs\Schemas\Referral\ReferralSchema;
use App\Docs\Tags\ReferralsTag;
use App\Models\Referral;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class IndexReferralOperation extends Operation
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
            ->summary('List all the referrals')
            ->description('**Permission:** `Service Worker`')
            ->parameters(
                PageParameter::create(),
                PerPageParameter::create(),
                FilterIdParameter::create(),
                FilterParameter::create(null, 'service_id')
                    ->description('Comma separated list of service IDs to filter by')
                    ->schema(
                        Schema::array()->items(
                            Schema::string()->format(Schema::FORMAT_UUID)
                        )
                    )
                    ->style(FilterParameter::STYLE_SIMPLE),
                FilterParameter::create(null, 'reference')
                    ->description('The reference for the referral to filter by')
                    ->schema(Schema::string()),
                FilterParameter::create(null, 'service_name')
                    ->description('The service name for the referral to filter by')
                    ->schema(Schema::string()),
                FilterParameter::create(null, 'organisation_name')
                    ->description('The organisation name for the referral to filter by')
                    ->schema(Schema::string()),
                FilterParameter::create(null, 'status')
                    ->description('The status for the referral to filter by')
                    ->schema(
                        Schema::string()->enum(
                            Referral::STATUS_NEW,
                            Referral::STATUS_IN_PROGRESS,
                            Referral::STATUS_COMPLETED,
                            Referral::STATUS_INCOMPLETED
                        )
                    ),
                IncludeParameter::create(null, ['service.organisation']),
                SortParameter::create(null, [
                    'reference',
                    'service_name',
                    'organisation_name',
                    'status',
                    'created_at',
                ], '-created_at')
            )
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        PaginationSchema::create(null, ReferralSchema::create())
                    )
                )
            );
    }
}
