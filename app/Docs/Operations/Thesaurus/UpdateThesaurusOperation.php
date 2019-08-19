<?php

namespace App\Docs\Operations\Thesaurus;

use App\Docs\Schemas\Thesaurus\ThesaurusSchema;
use App\Docs\Schemas\Thesaurus\UpdateThesaurusSchema;
use App\Docs\Tags\SearchEngineTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class UpdateThesaurusOperation extends Operation
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
            ->tags(SearchEngineTag::create())
            ->summary('Update the list of synonyms')
            ->description('**Permission**: `Super Admin`')
            ->requestBody(
                RequestBody::create()
                    ->required()
                    ->content(
                        MediaType::json()->schema(UpdateThesaurusSchema::create())
                    )
            )
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(ThesaurusSchema::create())
                )
            );
    }
}
