<?php

namespace App\Docs\Paths\Referrals;

use App\Docs\Operations\Referrals\IndexReferralOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class ReferralsIndexPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/referrals/index')
            ->operations(
                IndexReferralOperation::create()
                    ->action(IndexReferralOperation::ACTION_POST)
                    ->description(
                        <<<'EOT'
This is an alias of `GET /referrals` which allows all the query string parameters to be passed as 
part of the request body.
EOT
                    )
            );
    }
}
