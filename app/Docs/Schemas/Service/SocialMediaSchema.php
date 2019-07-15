<?php

namespace App\Docs\Schemas\Service;

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
                    ->enum('twitter', 'facebook', 'instagram', 'youtube', 'other')
                    ->example('instagram'),
                Schema::string('url')
                    ->example('https://www.instagram.com/ayupdigital')
            );
    }
}
