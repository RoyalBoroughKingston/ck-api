<?php

namespace App\Docs\Operations\Collections\Personas;

use App\Docs\Responses\PngResponse;
use App\Docs\Tags\CollectionPersonasTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;

class ImageCollectionPersonaOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->action(static::ACTION_GET)
            ->tags(CollectionPersonasTag::create())
            ->summary("Get a specific persona collection's image")
            ->description('**Permission:** `Open`')
            ->noSecurity()
            ->responses(PngResponse::create());
    }
}
