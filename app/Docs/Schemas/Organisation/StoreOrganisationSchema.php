<?php

namespace App\Docs\Schemas\Organisation;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class StoreOrganisationSchema extends Schema
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
                'url',
                'email',
                'phone'
            )
            ->properties(
                Schema::string('name')
                    ->example('Ayup Digital'),
                Schema::string('slug')
                    ->example('ayup-digital'),
                Schema::string('description')
                    ->example('Digital product agency'),
                Schema::string('url')
                    ->example('https://ayup.agency'),
                Schema::string('email')
                    ->example('info@ayup.agency'),
                Schema::string('phone')
                    ->example('01138591020'),
                Schema::string('logo_file_id')
                    ->format(Schema::FORMAT_UUID)
                    ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b')
                    ->description('The ID of the file uploaded')
                    ->nullable()
            );
    }
}
