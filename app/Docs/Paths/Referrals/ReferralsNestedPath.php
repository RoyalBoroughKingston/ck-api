<?php

namespace App\Docs\Paths\Referrals;

use App\Docs\Operations\Referrals\DestroyReferralOperation;
use App\Docs\Operations\Referrals\ShowReferralOperation;
use App\Docs\Operations\Referrals\UpdateReferralOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

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
            ->operations(
                ShowReferralOperation::create(),
                UpdateReferralOperation::create(),
                DestroyReferralOperation::create()
            );
    }
}
