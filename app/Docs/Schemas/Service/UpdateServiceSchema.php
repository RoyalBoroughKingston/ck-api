<?php

namespace App\Docs\Schemas\Service;

use App\Models\Service;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateServiceSchema extends Schema
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
                'name',
                'slug',
                'type',
                'status',
                'intro',
                'description',
                'wait_time',
                'is_free',
                'fees_text',
                'fees_url',
                'testimonial',
                'video_embed',
                'url',
                'contact_name',
                'contact_phone',
                'contact_email',
                'show_referral_disclaimer',
                'referral_method',
                'referral_button_text',
                'referral_email',
                'referral_url',
                'criteria',
                'useful_infos',
                'offerings',
                'social_medias',
                'gallery_items',
                'category_taxonomies'
            )
            ->properties(
                Schema::string('name'),
                Schema::string('slug'),
                Schema::string('type')
                    ->enum(
                        Service::TYPE_SERVICE,
                        Service::TYPE_ACTIVITY,
                        Service::TYPE_CLUB,
                        Service::TYPE_GROUP
                    ),
                Schema::string('status')
                    ->enum(Service::STATUS_ACTIVE, Service::STATUS_INACTIVE),
                Schema::string('intro'),
                Schema::string('description'),
                Schema::string('wait_time')
                    ->enum(
                        Service::WAIT_TIME_ONE_WEEK,
                        Service::WAIT_TIME_TWO_WEEKS,
                        Service::WAIT_TIME_THREE_WEEKS,
                        Service::WAIT_TIME_MONTH,
                        Service::WAIT_TIME_LONGER
                    )
                    ->nullable(),
                Schema::boolean('is_free'),
                Schema::string('fees_text')
                    ->nullable(),
                Schema::string('fees_url')
                    ->nullable(),
                Schema::string('testimonial')
                    ->nullable(),
                Schema::string('video_embed')
                    ->nullable(),
                Schema::string('url')
                    ->nullable(),
                Schema::string('contact_name'),
                Schema::string('contact_phone'),
                Schema::string('contact_email'),
                Schema::boolean('show_referral_disclaimer'),
                Schema::string('referral_method')
                    ->enum(
                        Service::REFERRAL_METHOD_INTERNAL,
                        Service::REFERRAL_METHOD_EXTERNAL,
                        Service::REFERRAL_METHOD_NONE
                    ),
                Schema::string('referral_button_text')
                    ->nullable(),
                Schema::string('referral_email')
                    ->nullable(),
                Schema::string('referral_url')
                    ->nullable(),
                Schema::string('logo_file_id')
                    ->format(Schema::FORMAT_UUID)
                    ->description('The ID of the file uploaded')
                    ->nullable(),
                Schema::object('criteria')
                    ->required(
                        'age_group',
                        'disability',
                        'employment',
                        'gender',
                        'housing',
                        'income',
                        'language',
                        'other'
                    )
                    ->properties(
                        Schema::string('age_group')
                            ->nullable(),
                        Schema::string('disability')
                            ->nullable(),
                        Schema::string('employment')
                            ->nullable(),
                        Schema::string('gender')
                            ->nullable(),
                        Schema::string('housing')
                            ->nullable(),
                        Schema::string('income')
                            ->nullable(),
                        Schema::string('language')
                            ->nullable(),
                        Schema::string('other')
                            ->nullable()
                    ),
                Schema::array('useful_infos')
                    ->items(
                        UsefulInfoSchema::create()
                            ->required('title', 'description', 'order')
                    ),
                Schema::array('offerings')
                    ->items(
                        OfferingSchema::create()
                            ->required('offering', 'order')
                    ),
                Schema::array('social_medias')
                    ->items(
                        SocialMediaSchema::create()
                            ->required('type', 'url')
                    ),
                Schema::array('gallery_items')
                    ->items(
                        Schema::object()->properties(
                            Schema::string('file_id')
                                ->type(Schema::FORMAT_UUID)
                        )
                    ),
                Schema::array('category_taxonomies')
                    ->items(
                        Schema::string()
                            ->type(Schema::FORMAT_UUID)
                    )
            );
    }
}
