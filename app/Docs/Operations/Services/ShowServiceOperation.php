<?php

namespace App\Docs\Operations\Services;

use App\Docs\Parameters\IncludeParameter;
use App\Docs\Schemas\ResourceSchema;
use App\Docs\Schemas\Service\ServiceSchema;
use App\Docs\Tags\ServicesTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ShowServiceOperation extends Operation
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
            ->tags(ServicesTag::create())
            ->summary('Get a specific service')
            ->description('**Permission:** `Open`')
            ->noSecurity()
            ->parameters(
                Parameter::path()
                    ->name('service')
                    ->description('The ID or slug of the service')
                    ->schema(Schema::string()),
                IncludeParameter::create(null, ['organisation'])
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
