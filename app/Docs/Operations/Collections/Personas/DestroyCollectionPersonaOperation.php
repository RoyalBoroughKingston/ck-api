<?php

namespace App\Docs\Operations\Collections\Personas;

use App\Docs\Responses\ResourceDeletedResponse;
use App\Docs\Tags\CollectionPersonasTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class DestroyCollectionPersonaOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return \GoldSpecDigital\ObjectOrientedOAS\Objects\Operation
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->action(static::ACTION_DELETE)
            ->tags(CollectionPersonasTag::create())
            ->summary('Delete a specific persona collection')
            ->description('**Permission:** `Super Admin`')
            ->parameters(
                Parameter::path()
                    ->name('persona')
                    ->description('The ID of the persona collection')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID))
            )
            ->responses(
                ResourceDeletedResponse::create(null, 'collection persona')
            );
    }
}
