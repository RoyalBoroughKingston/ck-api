<?php

namespace App\Docs\Operations\Locations;

use App\Docs\Parameters\FilterIdParameter;
use App\Docs\Parameters\FilterParameter;
use App\Docs\Parameters\PageParameter;
use App\Docs\Parameters\PerPageParameter;
use App\Docs\Parameters\SortParameter;
use App\Docs\Schemas\Location\LocationSchema;
use App\Docs\Schemas\PaginationSchema;
use App\Docs\Tags\LocationsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class IndexLocationOperation extends Operation
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
            ->tags(LocationsTag::create())
            ->summary('List all the locations')
            ->description('**Permission:** `Open`')
            ->noSecurity()
            ->parameters(
                PageParameter::create(),
                PerPageParameter::create(),
                FilterIdParameter::create(),
                FilterParameter::create(null, 'address_line_1')
                    ->description('Filter by address line 1')
                    ->schema(Schema::string()),
                FilterParameter::create(null, 'address_line_2')
                    ->description('Filter by address line 2')
                    ->schema(Schema::string()),
                FilterParameter::create(null, 'address_line_3')
                    ->description('Filter by address line 3')
                    ->schema(Schema::string()),
                FilterParameter::create(null, 'city')
                    ->description('Filter by city')
                    ->schema(Schema::string()),
                FilterParameter::create(null, 'county')
                    ->description('Filter by county')
                    ->schema(Schema::string()),
                FilterParameter::create(null, 'postcode')
                    ->description('Filter by postcode')
                    ->schema(Schema::string()),
                FilterParameter::create(null, 'country')
                    ->description('Filter by country')
                    ->schema(Schema::string()),
                SortParameter::create(null, [
                    'address_line_1',
                    'address_line_2',
                    'address_line_3',
                    'city',
                    'county',
                    'postcode',
                    'country',
                ], 'address_line_1,address_line_2,address_line_3,city,county,postcode,country')
            )
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        PaginationSchema::create(null, LocationSchema::create())
                    )
                )
            );
    }
}
