<?php

namespace App\Docs\Paths\Referrals;

use App\Docs\Operations\Referrals\IndexReferralOperation;
use App\Docs\Operations\Referrals\StoreReferralOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class ReferralsRootPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/referrals')
            ->operations(
                IndexReferralOperation::create(),
                StoreReferralOperation::create()
            );
    }
}
