<?php

namespace App\Docs\Schemas\Referral;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class StoreReferralSchema extends Schema
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return \GoldSpecDigital\ObjectOrientedOAS\Objects\Schema
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
                    ->format(static::FORMAT_UUID)
                    ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b'),
                Schema::string('name')
                    ->example('John Doe'),
                Schema::string('email')
                    ->nullable()
                    ->example('jonh.doe@example.com'),
                Schema::string('phone')
                    ->nullable()
                    ->example(null),
                Schema::string('other_contact')
                    ->nullable()
                    ->example(null),
                Schema::string('postcode_outward_code')
                    ->nullable()
                    ->example('LS6'),
                Schema::string('comments')
                    ->nullable()
                    ->example(null),
                Schema::boolean('referral_consented')
                    ->example(false),
                Schema::string('referee_name')
                    ->nullable()
                    ->example('Foo Bar'),
                Schema::string('referee_email')
                    ->nullable()
                    ->example('foo.bar@example.com'),
                Schema::string('referee_phone')
                    ->nullable()
                    ->example('01138591020'),
                Schema::string('organisation_taxonomy_id')
                    ->format(Schema::FORMAT_UUID)
                    ->nullable()
                    ->example(null),
                Schema::string('organisation')
                    ->nullable()
                    ->example('Ayup Digital')
            );
    }
}
