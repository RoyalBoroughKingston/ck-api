<?php

namespace App\Docs\Operations\Audits;

use App\Docs\Parameters\IncludeParameter;
use App\Docs\Schemas\Audit\AuditSchema;
use App\Docs\Schemas\ResourceSchema;
use App\Docs\Tags\AuditsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ShowAuditOperation extends Operation
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
            ->summary('Get a specific audit')
            ->description('**Permission:** `Global Admin`')
            ->parameters(
                Parameter::path()
                    ->name('audit')
                    ->description('The ID of the audit')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID)),
                IncludeParameter::create(null, ['user'])
            )
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        ResourceSchema::create(null, AuditSchema::create())
                    )
                )
            );
    }
}
