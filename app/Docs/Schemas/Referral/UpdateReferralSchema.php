<?php

namespace App\Docs\Schemas\Referral;

use App\Models\Referral;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateReferralSchema extends Schema
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->type(static::TYPE_OBJECT)
            ->required('status', 'comments')
            ->properties(
                Schema::string('status')
                    ->enum(
                        Referral::STATUS_NEW,
                        Referral::STATUS_IN_PROGRESS,
                        Referral::STATUS_COMPLETED,
                        Referral::STATUS_INCOMPLETED
                    ),
                Schema::string('comments')
            );
    }
}
