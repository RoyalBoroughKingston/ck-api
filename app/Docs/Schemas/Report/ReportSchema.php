<?php

namespace App\Docs\Schemas\Report;

use App\Models\ReportType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ReportSchema extends Schema
{
    /**
     * @param string|null $objectId
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->type(static::TYPE_OBJECT)
            ->properties(
                Schema::string('id')
                    ->format(Schema::FORMAT_UUID),
                Schema::string('report_type')
                    ->enum(
                        ...ReportType::query()->pluck('name')->toArray()
                    ),
                Schema::string('starts_at')
                    ->format(Schema::FORMAT_DATE)
                    ->nullable(),
                Schema::string('ends_at')
                    ->format(Schema::FORMAT_DATE)
                    ->nullable(),
                Schema::string('created_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('updated_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable()
            );
    }
}
