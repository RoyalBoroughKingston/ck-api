<?php

namespace App\Docs\Schemas\Service;

use App\Docs\Schemas\Taxonomy\Category\TaxonomyCategorySchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ServiceSchema extends Schema
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
                Schema::string('organisation_id')
                    ->format(static::FORMAT_UUID)
                    ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b'),
                Schema::boolean('has_logo'),
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
                Schema::object('criteria')
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
                    ->items(UsefulInfoSchema::create()),
                Schema::array('offerings')
                    ->items(OfferingSchema::create()),
                Schema::array('social_medias')
                    ->items(SocialMediaSchema::create()),
                Schema::array('gallery_items')
                    ->items(GalleryItemSchema::create()),
                Schema::array('category_taxonomies')
                    ->items(TaxonomyCategorySchema::create()),
                Schema::string('last_modified_at')
                    ->format(Schema::FORMAT_DATE_TIME),
                Schema::string('created_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('updated_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable()
            );
    }
}
