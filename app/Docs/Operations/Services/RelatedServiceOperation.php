<?php

namespace App\Docs\Operations\Services;

use App\Docs\Parameters\IncludeParameter;
use App\Docs\Schemas\PaginationSchema;
use App\Docs\Schemas\Service\ServiceSchema;
use App\Docs\Tags\ServicesTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class RelatedServiceOperation extends Operation
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
            ->summary('Get related services to the one specified')
            ->description('**Permission:** `Open`')
            ->noSecurity()
            ->parameters(
                Parameter::query()
                    ->name('location[lat]')
                    ->description('The latitude to sort by')
                    ->schema(
                        Schema::number()
                            ->format(Schema::FORMAT_FLOAT)
                    ),
                Parameter::query()
                    ->name('location[lon]')
                    ->description('The longitude to sort by')
                    ->schema(
                        Schema::number()
                            ->format(Schema::FORMAT_FLOAT)
                    ),
                IncludeParameter::create(null, ['organisation'])
            )
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        PaginationSchema::create(null, ServiceSchema::create())
                    )
                )
            );
    }
}
