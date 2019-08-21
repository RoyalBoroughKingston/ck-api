<?php

namespace App\Docs\Operations\Search;

use App\Docs\Parameters\PageParameter;
use App\Docs\Parameters\PerPageParameter;
use App\Docs\Schemas\Location\LocationSchema;
use App\Docs\Schemas\PaginationSchema;
use App\Docs\Schemas\Search\StoreSearchSchema;
use App\Docs\Schemas\Service\ServiceSchema;
use App\Docs\Schemas\ServiceLocation\ServiceLocationSchema;
use App\Docs\Tags\SearchTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class StoreSearchOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        $serviceLocationSchema = ServiceLocationSchema::create();
        $serviceLocationSchema = $serviceLocationSchema->properties(
            LocationSchema::create('location'),
            ...$serviceLocationSchema->properties
        );

        $serviceSchema = ServiceSchema::create();
        $serviceSchema = $serviceSchema->properties(
            Schema::array('service_locations')->items($serviceLocationSchema),
            ...$serviceSchema->properties
        );

        return parent::create($objectId)
            ->action(static::ACTION_POST)
            ->tags(SearchTag::create())
            ->summary('Perform a search for services')
            ->description('**Permission:** `Open`')
            ->noSecurity()
            ->parameters(
                PageParameter::create(),
                PerPageParameter::create()
            )
            ->requestBody(
                RequestBody::create()
                    ->required()
                    ->content(
                        MediaType::json()->schema(StoreSearchSchema::create())
                    )
            )
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        PaginationSchema::create(null, $serviceSchema)
                    )
                )
            );
    }
}
