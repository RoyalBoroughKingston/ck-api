<?php

namespace App\Docs\Operations\Organisations;

use App\Docs\Parameters\FilterIdParameter;
use App\Docs\Parameters\FilterParameter;
use App\Docs\Parameters\PageParameter;
use App\Docs\Parameters\PerPageParameter;
use App\Docs\Parameters\SortParameter;
use App\Docs\Schemas\Organisation\OrganisationSchema;
use App\Docs\Schemas\PaginationSchema;
use App\Docs\Tags\OrganisationsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class IndexOrganisationOperation extends Operation
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
            ->tags(OrganisationsTag::create())
            ->summary('List all the organisations')
            ->description('**Permission:** `Open`')
            ->noSecurity()
            ->parameters(
                PageParameter::create(),
                PerPageParameter::create(),
                FilterIdParameter::create(),
                FilterParameter::create(null, 'name')
                    ->description('Name to filter by')
                    ->schema(Schema::string()),
                FilterParameter::create(null, 'has_permission')
                    ->description('Filter organisations to only ones they have permissions for')
                    ->schema(Schema::boolean()),
                SortParameter::create(null, ['name'], 'name')
            )
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        PaginationSchema::create(null, OrganisationSchema::create())
                    )
                )
            );
    }
}
