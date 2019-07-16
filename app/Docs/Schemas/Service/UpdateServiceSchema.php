<?php

namespace App\Docs\Schemas\Service;

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
                Schema::string('name')
                    ->example('Helping The Homeless'),
                Schema::string('slug')
                    ->example('helping-the-homeless'),
                Schema::string('type')
                    ->enum('service', 'activity', 'club', 'group')
                    ->example('service'),
                Schema::string('status')
                    ->enum('active', 'inactive')
                    ->example('active'),
                Schema::string('intro')
                    ->example('Lorem ipsum'),
                Schema::string('description')
                    ->example('Lorem ipsum'),
                Schema::string('wait_time')
                    ->enum('one_week', 'two_weeks', 'three_weeks', 'month', 'longer')
                    ->nullable()
                    ->example(null),
                Schema::boolean('is_free')
                    ->example(true),
                Schema::string('fees_text')
                    ->nullable()
                    ->example(null),
                Schema::string('fees_url')
                    ->nullable()
                    ->example(null),
                Schema::string('testimonial')
                    ->nullable()
                    ->example(null),
                Schema::string('video_embed')
                    ->nullable()
                    ->example(null),
                Schema::string('url')
                    ->nullable()
                    ->example(null),
                Schema::string('contact_name')
                    ->example('John Doe'),
                Schema::string('contact_phone')
                    ->example('01138591020'),
                Schema::string('contact_email')
                    ->example('info@ayup.agency'),
                Schema::boolean('show_referral_disclaimer')
                    ->example(true),
                Schema::string('referral_method')
                    ->enum('internal', 'external', 'none'),
                Schema::string('referral_button_text')
                    ->nullable()
                    ->example('Make Referral'),
                Schema::string('referral_email')
                    ->nullable()
                    ->example('info@ayup.agency'),
                Schema::string('referral_url')
                    ->nullable()
                    ->example(null),
                Schema::string('logo_file_id')
                    ->format(Schema::FORMAT_UUID)
                    ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b')
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
                            ->nullable()
                            ->example(null),
                        Schema::string('disability')
                            ->nullable()
                            ->example(null),
                        Schema::string('employment')
                            ->nullable()
                            ->example(null),
                        Schema::string('gender')
                            ->nullable()
                            ->example(null),
                        Schema::string('housing')
                            ->nullable()
                            ->example(null),
                        Schema::string('income')
                            ->nullable()
                            ->example(null),
                        Schema::string('language')
                            ->nullable()
                            ->example(null),
                        Schema::string('other')
                            ->nullable()
                            ->example(null)
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
                                ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b')
                        )
                    ),
                Schema::array('category_taxonomies')
                    ->items(
                        Schema::string()
                            ->type(Schema::FORMAT_UUID)
                            ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b')
                    )
            );
    }
}
