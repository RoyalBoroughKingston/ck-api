<?php

namespace App\Docs\Operations\Services;

use App\Docs\Responses\UpdateRequestReceivedResponse;
use App\Docs\Schemas\Service\UpdateServiceSchema;
use App\Docs\Tags\ServicesTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateServiceOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        $updateServiceSchema = UpdateServiceSchema::create();
        $updateServiceSchema = $updateServiceSchema->properties(
            Schema::boolean('preview')
                ->default(false)
                ->description('When enabled, only a preview of the update request will be generated'),
            ...$updateServiceSchema->properties
        );

        return parent::create($objectId)
            ->action(static::ACTION_PUT)
            ->tags(ServicesTag::create())
            ->summary('Update a specific service')
            ->description(
                <<<'EOT'
**Permission:** `Service Admin`
- Can update a service location but not it's taxonomies

**Permission:** `Global Admin`
- Can update a service location
EOT
            )
            ->requestBody(
                RequestBody::create()
                    ->required()
                    ->content(
                        MediaType::json()->schema($updateServiceSchema)
                    )
            )
            ->responses(
                UpdateRequestReceivedResponse::create(null, UpdateServiceSchema::create())
            );
    }
}
