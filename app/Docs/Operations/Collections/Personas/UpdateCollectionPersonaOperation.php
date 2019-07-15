<?php

namespace App\Docs\Operations\Collections\Personas;

use App\Docs\Schemas\Collection\Persona\CollectionPersonaSchema;
use App\Docs\Schemas\Collection\Persona\UpdateCollectionPersonaSchema;
use App\Docs\Schemas\ResourceSchema;
use App\Docs\Tags\CollectionPersonasTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateCollectionPersonaOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->action(static::ACTION_PUT)
            ->tags(CollectionPersonasTag::create())
            ->summary('Update a specific persona collection')
            ->description('**Permission:** `Global Admin`')
            ->parameters(
                Parameter::path()
                    ->name('persona')
                    ->description('The ID of the persona collection')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID))
            )
            ->requestBody(
                RequestBody::create()
                    ->required()
                    ->content(
                        MediaType::json()->schema(
                            UpdateCollectionPersonaSchema::create()
                        )
                    )
            )
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        ResourceSchema::create(null, CollectionPersonaSchema::create())
                    )
                )
            );
    }
}
