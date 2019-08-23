<?php

namespace App\Docs\Schemas\PageFeedback;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdatePageFeedbackSchema extends Schema
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
                'url',
                'feedback',
                'name',
                'email',
                'phone'
            )
            ->properties(
                Schema::string('url'),
                Schema::string('feedback'),
                Schema::string('name')
                    ->nullable(),
                Schema::string('email')
                    ->nullable(),
                Schema::string('phone')
                    ->nullable()
            );
    }
}
