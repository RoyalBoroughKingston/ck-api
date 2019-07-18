<?php

namespace App\Docs\Schemas\Referral;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class StoreReferralSchema extends Schema
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
            ->required(
                'service_id',
                'name',
                'email',
                'phone',
                'other_contact',
                'postcode_outward_code',
                'comments',
                'referral_consented',
                'feedback_consented',
                'referee_name',
                'referee_email',
                'referee_phone',
                'organisation_taxonomy_id',
                'organisation'
            )
            ->properties(
                Schema::string('service_id')
                    ->format(static::FORMAT_UUID),
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
                Schema::boolean('referral_consented'),
                Schema::string('referee_name')
                    ->nullable(),
                Schema::string('referee_email')
                    ->nullable(),
                Schema::string('referee_phone')
                    ->nullable(),
                Schema::string('organisation_taxonomy_id')
                    ->format(Schema::FORMAT_UUID)
                    ->nullable(),
                Schema::string('organisation')
                    ->nullable()
            );
    }
}
