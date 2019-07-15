<?php

namespace App\Docs\Schemas\PageFeedback;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class PageFeedbackSchema extends Schema
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
                    ->format(Schema::FORMAT_UUID)
                    ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b'),
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
                    ->example('07700000000'),
                Schema::string('consented_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('created_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('updated_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable()
            );
    }
}
