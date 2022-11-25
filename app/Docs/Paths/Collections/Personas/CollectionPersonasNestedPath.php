<?php

namespace App\Docs\Paths\Collections\Personas;

use App\Docs\Operations\Collections\Personas\DestroyCollectionPersonaOperation;
use App\Docs\Operations\Collections\Personas\ShowCollectionPersonaOperation;
use App\Docs\Operations\Collections\Personas\UpdateCollectionPersonaOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

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
            ->parameters(
                Parameter::path()
                    ->name('persona')
                    ->description('The ID or slug of the persona collection')
                    ->required()
                    ->schema(Schema::string())
            )
            ->operations(
                ShowCollectionPersonaOperation::create(),
                UpdateCollectionPersonaOperation::create(),
                DestroyCollectionPersonaOperation::create()
            );
    }
}
