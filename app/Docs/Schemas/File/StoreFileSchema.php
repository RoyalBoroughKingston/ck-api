<?php

namespace App\Docs\Schemas\File;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class StoreFileSchema extends Schema
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
                'is_private',
                'mime_type',
                'file'
            )
            ->properties(
                Schema::boolean('is_private'),
                Schema::string('mime_type')
                    ->enum('image/png'),
                Schema::string('file')
                    ->format(static::FORMAT_BINARY)
                    ->description('Base64 encoded string of the image')
                    ->example('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z/C/HgAGgwJ/lK3Q6wAAAABJRU5ErkJggg==')
            );
    }
}
