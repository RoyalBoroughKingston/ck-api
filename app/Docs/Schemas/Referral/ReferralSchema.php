<?php

namespace App\Docs\Schemas\Referral;

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
                    ->format(static::FORMAT_UUID)
                    ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b'),
                Schema::string('service_id')
                    ->format(static::FORMAT_UUID)
                    ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b'),
                Schema::string('reference')
                    ->example('CKSCA23JKJ'),
                Schema::string('status')
                    ->enum('new', 'in_progress', 'completed', 'incompleted')
                    ->example('new'),
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
                Schema::string('referral_consented_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('feedback_consented_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable()
                    ->example(null),
                Schema::string('referee_name')
                    ->nullable()
                    ->example('Foo Bar'),
                Schema::string('referee_email')
                    ->nullable()
                    ->example('foo.bar@example.com'),
                Schema::string('referee_phone')
                    ->nullable()
                    ->example('01138591020'),
                Schema::string('referee_organisation')
                    ->nullable()
                    ->example('Ayup Digital'),
                Schema::string('created_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('updated_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable()
            );
    }
}
