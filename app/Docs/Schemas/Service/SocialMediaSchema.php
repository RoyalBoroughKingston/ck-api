<?php

namespace App\Docs\Schemas\Service;

use App\Models\SocialMedia;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class SocialMediaSchema extends Schema
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
                Schema::string('type')
                    ->enum(
                        SocialMedia::TYPE_TWITTER,
                        SocialMedia::TYPE_FACEBOOK,
                        SocialMedia::TYPE_INSTAGRAM,
                        SocialMedia::TYPE_YOUTUBE,
                        SocialMedia::TYPE_OTHER
                    ),
                Schema::string('url')
            );
    }
}
