<?php

namespace App\Docs\Schemas\Organisation;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateOrganisationSchema extends Schema
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
                'description',
                'url'
            )
            ->properties(
                Schema::string('name'),
                Schema::string('slug'),
                Schema::string('description'),
                Schema::string('url'),
                Schema::string('email')
                    ->nullable(),
                Schema::string('phone')
                    ->nullable(),
                Schema::string('logo_file_id')
                    ->format(Schema::FORMAT_UUID)
                    ->description('The ID of the file uploaded')
                    ->nullable()
            );
    }
}
