<?php

namespace App\Docs\Schemas\Setting;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateSettingSchema extends Schema
{
    /**
     * @param string|null $objectId
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        $global = Schema::object('global')
            ->required(
                'footer_title',
                'footer_content',
                'contact_phone',
                'contact_email',
                'facebook_handle',
                'twitter_handle'
            )
            ->properties(
                Schema::string('footer_title'),
                Schema::string('footer_content')->format('markdown'),
                Schema::string('contact_phone'),
                Schema::string('contact_email'),
                Schema::string('facebook_handle'),
                Schema::string('twitter_handle')
            );

        $home = Schema::object('home')
            ->required(
                'search_title',
                'categories_title',
                'personas_title',
                'personas_content'
            )
            ->properties(
                Schema::string('search_title'),
                Schema::string('categories_title'),
                Schema::string('personas_title'),
                Schema::string('personas_content')->format('markdown')
            );

        $termsAndConditions = Schema::object('terms_and_conditions')
            ->required(
                'title',
                'content'
            )
            ->properties(
                Schema::string('title'),
                Schema::string('content')->format('markdown')
        );

        $privacyPolicy = Schema::object('privacy_policy')
            ->required(
                'title',
                'content'
            )
            ->properties(
                Schema::string('title'),
                Schema::string('content')->format('markdown')
            );

        $about = Schema::object('about')
            ->required(
                'title',
                'content',
                'video_url'
            )
            ->properties(
                Schema::string('title'),
                Schema::string('content')->format('markdown'),
                Schema::string('video_url')
            );

        $contact = Schema::object('contact')
            ->required(
                'title',
                'content'
            )
            ->properties(
                Schema::string('title'),
                Schema::string('content')->format('markdown')
            );

        $getInvolved = Schema::object('get_involved')
            ->required(
                'title',
                'content'
            )
            ->properties(
                Schema::string('title'),
                Schema::string('content')->format('markdown')
            );

        $favourites = Schema::object('favourites')
            ->required(
                'title',
                'content'
            )
            ->properties(
                Schema::string('title'),
                Schema::string('content')->format('markdown')
            );

        $banner = Schema::object('banner')
            ->required(
                'title',
                'content',
                'button_text',
                'button_url'
            )
            ->properties(
                Schema::string('title'),
                Schema::string('content')->format('markdown'),
                Schema::string('button_text'),
                Schema::string('button_url'),
                Schema::string('image_file_id')
                    ->format(Schema::FORMAT_UUID)
                    ->description('The ID of the file uploaded')
                    ->nullable()
            );

        return parent::create($objectId)
            ->type(static::TYPE_OBJECT)
            ->required('cms')
            ->properties(
                Schema::object('cms')
                    ->required('frontend')
                    ->properties(
                        Schema::object('frontend')
                            ->required(
                                'global',
                                'home',
                                'terms_and_conditions',
                                'privacy_policy',
                                'about',
                                'contact',
                                'get_involved',
                                'favourites',
                                'banner'
                            )
                            ->properties(
                                $global,
                                $home,
                                $termsAndConditions,
                                $privacyPolicy,
                                $about,
                                $contact,
                                $getInvolved,
                                $favourites,
                                $banner
                            )
                    )
            );
    }
}
