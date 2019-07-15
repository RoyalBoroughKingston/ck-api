<?php

namespace App\Docs\Paths\Collections\Personas;

use App\Docs\Operations\Collections\Personas\ImageCollectionPersonaOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class CollectionPersonasImagePath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/collections/personas/{persona}/image.png')
            ->operations(
                ImageCollectionPersonaOperation::create()
            );
    }
}
