<?php

namespace App\Docs\Schemas\Service;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class RefreshServiceSchema extends Schema
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
            ->required('token')
            ->properties(
                Schema::string('token')
                    ->format(Schema::FORMAT_UUID)
                    ->description('A unique one-time token needed to invoke the refresh')
            );
    }
}
