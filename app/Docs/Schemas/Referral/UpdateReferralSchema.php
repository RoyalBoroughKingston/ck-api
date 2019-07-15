<?php

namespace App\Docs\Schemas\Referral;

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
                    ->enum('new', 'in_progress', 'completed', 'incompleted')
                    ->example('in_progress'),
                Schema::string('comments')
                    ->example('Assigned to me')
            );
    }
}
