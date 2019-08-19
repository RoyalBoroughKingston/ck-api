<?php

namespace App\Docs\Operations\Services;

use App\Docs\Parameters\FilterIdParameter;
use App\Docs\Parameters\FilterParameter;
use App\Docs\Parameters\IncludeParameter;
use App\Docs\Parameters\PageParameter;
use App\Docs\Parameters\PerPageParameter;
use App\Docs\Parameters\SortParameter;
use App\Docs\Schemas\PaginationSchema;
use App\Docs\Schemas\Service\ServiceSchema;
use App\Docs\Tags\ServicesTag;
use App\Models\Service;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class IndexServiceOperation extends Operation
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
            ->summary('List all the services')
            ->description(
                <<<'EOT'
**Permission:** `Open`

---

Services are returned in ascending order of their name.

Guests are limited to only view active services.
EOT
            )
            ->noSecurity()
            ->parameters(
                PageParameter::create(),
                PerPageParameter::create(),
                FilterIdParameter::create(),
                FilterParameter::create(null, 'organisation_id')
                    ->description('Comma separated list of organisation IDs to filter by')
                    ->schema(
                        Schema::array()->items(
                            Schema::string()->format(Schema::FORMAT_UUID)
                        )
                    )
                    ->style(FilterParameter::STYLE_SIMPLE),
                FilterParameter::create(null, 'name')
                    ->description('Name to filter by')
                    ->schema(Schema::string()),
                FilterParameter::create(null, 'organisation_name')
                    ->description('Organisation name to filter by')
                    ->schema(Schema::string()),
                FilterParameter::create(null, 'status')
                    ->description('Status to filter by')
                    ->schema(
                        Schema::string()->enum(
                            Service::STATUS_ACTIVE,
                            Service::STATUS_INACTIVE
                        )
                    ),
                FilterParameter::create(null, 'referral_method')
                    ->description('Referral method to filter by')
                    ->schema(
                        Schema::string()->enum(
                            Service::REFERRAL_METHOD_INTERNAL,
                            Service::REFERRAL_METHOD_EXTERNAL,
                            Service::REFERRAL_METHOD_NONE
                        )
                    ),
                FilterParameter::create(null, 'has_permission')
                    ->description('Filter services to only ones they have permissions for')
                    ->schema(Schema::boolean()),
                IncludeParameter::create(null, ['organisation']),
                SortParameter::create(null, [
                    'name',
                    'organisation_name',
                    'status',
                    'referral_method',
                ], 'name')
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
