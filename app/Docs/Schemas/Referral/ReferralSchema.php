<?php

namespace App\Docs\Schemas\Referral;

use App\Models\Referral;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ReferralSchema extends Schema
{
    /**
     * @param string|null $objectId
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->type(static::TYPE_OBJECT)
            ->properties(
                Schema::string('id')
                    ->format(static::FORMAT_UUID),
                Schema::string('service_id')
                    ->format(static::FORMAT_UUID),
                Schema::string('reference'),
                Schema::string('status')
                    ->enum(
                        Referral::STATUS_NEW,
                        Referral::STATUS_IN_PROGRESS,
                        Referral::STATUS_COMPLETED,
                        Referral::STATUS_INCOMPLETED
                    ),
                Schema::string('name'),
                Schema::string('email')
                    ->nullable(),
                Schema::string('phone')
                    ->nullable(),
                Schema::string('other_contact')
                    ->nullable(),
                Schema::string('postcode_outward_code')
                    ->nullable(),
                Schema::string('comments')
                    ->nullable(),
                Schema::string('referral_consented_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('feedback_consented_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('referee_name')
                    ->nullable(),
                Schema::string('referee_email')
                    ->nullable(),
                Schema::string('referee_phone')
                    ->nullable(),
                Schema::string('referee_organisation')
                    ->nullable(),
                Schema::string('created_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('updated_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable()
            );
    }
}
