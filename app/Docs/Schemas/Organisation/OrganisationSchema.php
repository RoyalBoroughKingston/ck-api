<?php

namespace App\Docs\Schemas\Organisation;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class OrganisationSchema extends Schema
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
                    ->format(Schema::TYPE_OBJECT)
                    ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b'),
                Schema::boolean('has_logo'),
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
                Schema::string('created_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('updated_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable()
            );
    }
}
