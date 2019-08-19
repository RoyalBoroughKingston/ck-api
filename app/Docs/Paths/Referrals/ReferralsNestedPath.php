<?php

namespace App\Docs\Paths\Referrals;

use App\Docs\Operations\Referrals\DestroyReferralOperation;
use App\Docs\Operations\Referrals\ShowReferralOperation;
use App\Docs\Operations\Referrals\UpdateReferralOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ReferralsNestedPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/referrals/{referral}')
            ->parameters(
                Parameter::path()
                    ->name('referral')
                    ->description('The ID of the referral')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID))
            )
            ->operations(
                ShowReferralOperation::create(),
                UpdateReferralOperation::create(),
                DestroyReferralOperation::create()
            );
    }
}
