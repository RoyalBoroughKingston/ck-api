<?php

namespace App\Docs\Paths\Collections\Personas;

use App\Docs\Operations\Collections\Personas\DestroyCollectionPersonaOperation;
use App\Docs\Operations\Collections\Personas\ShowCollectionPersonaOperation;
use App\Docs\Operations\Collections\Personas\UpdateCollectionPersonaOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class CollectionPersonasNestedPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/collections/personas/{persona}')
            ->operations(
                ShowCollectionPersonaOperation::create(),
                UpdateCollectionPersonaOperation::create(),
                DestroyCollectionPersonaOperation::create()
            );
    }
}
