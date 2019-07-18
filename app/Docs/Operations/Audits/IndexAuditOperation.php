<?php

namespace App\Docs\Operations\Audits;

use App\Docs\Parameters\FilterIdParameter;
use App\Docs\Parameters\FilterParameter;
use App\Docs\Parameters\IncludeParameter;
use App\Docs\Parameters\PageParameter;
use App\Docs\Parameters\PerPageParameter;
use App\Docs\Parameters\SortParameter;
use App\Docs\Schemas\Audit\AuditSchema;
use App\Docs\Schemas\PaginationSchema;
use App\Docs\Tags\AuditsTag;
use App\Models\Audit;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class IndexAuditOperation extends Operation
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
            ->tags(AuditsTag::create())
            ->summary('List all the audits')
            ->description('**Permission:** `Global Admin`')
            ->parameters(
                PageParameter::create(),
                PerPageParameter::create(),
                FilterIdParameter::create(),
                FilterParameter::create(null, 'user_id')
                    ->description('Comma separated list of user IDs to filter by')
                    ->schema(
                        Schema::array()->items(
                            Schema::string()->format(Schema::FORMAT_UUID)
                        )
                    )
                    ->style(FilterParameter::STYLE_SIMPLE),
                FilterParameter::create(null, 'oauth_client_id')
                    ->description('Comma separated list of OAuth client IDs to filter by')
                    ->schema(
                        Schema::array()->items(
                            Schema::string()->format(Schema::FORMAT_UUID)
                        )
                    )
                    ->style(FilterParameter::STYLE_SIMPLE),
                FilterParameter::create(null, 'action')
                    ->description('Action to filter by')
                    ->schema(
                        Schema::string()->enum(
                            Audit::ACTION_CREATE,
                            Audit::ACTION_READ,
                            Audit::ACTION_UPDATE,
                            Audit::ACTION_DELETE
                        )
                    ),
                FilterParameter::create(null, 'description')
                    ->description('Description to filter by')
                    ->schema(Schema::string()),
                IncludeParameter::create(null, ['user']),
                SortParameter::create(null, [
                    'action',
                    'description',
                    'user_full_name',
                    'created_at',
                ], '-created_at')
            )
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        PaginationSchema::create(null, AuditSchema::create())
                    )
                )
            );
    }
}
