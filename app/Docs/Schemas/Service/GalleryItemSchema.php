<?php

namespace App\Docs\Schemas\Service;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class GalleryItemSchema extends Schema
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
                Schema::string('file_id')
                    ->format(Schema::FORMAT_UUID)
                    ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b'),
                Schema::string('url')
                    ->example(
                        route('core.v1.services.gallery-items', [
                            'service' => '38e06e93-79b2-4c38-85bf-7749ebc7044b',
                            'file' => '38e06e93-79b2-4c38-85bf-7749ebc7044b',
                        ])
                    ),
                Schema::string('created_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('updated_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable()
            );
    }
}