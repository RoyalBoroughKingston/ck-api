<?php

namespace App\Docs\Schemas\Report;

use App\Models\ReportType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class StoreReportSchema extends Schema
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->type(static::TYPE_OBJECT)
            ->required('report_type')
            ->properties(
                Schema::string('report_type')
                    ->enum(
                        ...ReportType::query()->pluck('name')->toArray()
                    ),
                Schema::string('starts_at')
                    ->format(Schema::FORMAT_DATE),
                Schema::string('ends_at')
                    ->format(Schema::FORMAT_DATE)
            );
    }
}
