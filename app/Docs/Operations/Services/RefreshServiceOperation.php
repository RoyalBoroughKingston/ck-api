<?php

namespace App\Docs\Operations\Services;

use App\Docs\Schemas\ResourceSchema;
use App\Docs\Schemas\Service\RefreshServiceSchema;
use App\Docs\Schemas\Service\ServiceSchema;
use App\Docs\Tags\ServicesTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class RefreshServiceOperation extends Operation
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
            ->tags(ServicesTag::create())
            ->summary("Refresh a specific service's last modified timestamp to now")
            ->description('**Permission:** `Open`')
            ->noSecurity()
            ->requestBody(
                RequestBody::create()
                    ->required()
                    ->content(
                        MediaType::json()->schema(RefreshServiceSchema::create())
                    )
            )
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        ResourceSchema::create(null, ServiceSchema::create())
                    )
                )
            );
    }
}
