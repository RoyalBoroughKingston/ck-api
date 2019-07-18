<?php

namespace App\Docs\Operations\Collections\Personas;

use App\Docs\Schemas\Collection\Persona\CollectionPersonaSchema;
use App\Docs\Schemas\Collection\Persona\StoreCollectionPersonaSchema;
use App\Docs\Schemas\ResourceSchema;
use App\Docs\Tags\CollectionPersonasTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class StoreCollectionPersonaOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->action(static::ACTION_POST)
            ->tags(CollectionPersonasTag::create())
            ->summary('Create a persona collection')
            ->description('**Permission:** `Super Admin`')
            ->requestBody(
                RequestBody::create()
                    ->required()
                    ->content(
                        MediaType::json()->schema(
                            StoreCollectionPersonaSchema::create()
                        )
                    )
            )
            ->responses(
                Response::created()->content(
                    MediaType::json()->schema(
                        ResourceSchema::create(null, CollectionPersonaSchema::create())
                    )
                )
            );
    }
}
