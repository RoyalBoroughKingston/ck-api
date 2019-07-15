<?php

namespace App\Docs\Schemas\PageFeedback;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class StorePageFeedbackSchema extends Schema
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
                'url',
                'feedback',
                'name',
                'email',
                'phone'
            )
            ->properties(
                Schema::string('url')
                    ->example(url('/path/to/page')),
                Schema::string('feedback')
                    ->example('This does not work on my browser'),
                Schema::string('name')
                    ->nullable()
                    ->example('John Doe'),
                Schema::string('email')
                    ->nullable()
                    ->example('john.doe@example.com'),
                Schema::string('phone')
                    ->nullable()
                    ->example('07700000000')
            );
    }
}
