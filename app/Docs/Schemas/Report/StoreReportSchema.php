<?php

namespace App\Docs\Schemas\Report;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class StoreReportSchema extends Schema
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return \GoldSpecDigital\ObjectOrientedOAS\Objects\Schema
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->type(static::TYPE_OBJECT)
            ->required('report_type')
            ->properties(
                Schema::string('report_type')
                    ->enum(
                        'Users Export',
                        'Services Export',
                        'Organisations Export',
                        'Locations Export',
                        'Referrals Export',
                        'Feedback Export',
                        'Audit Logs Export',
                        'Search Histories Export',
                        'Thesaurus Export'
                    ),
                Schema::string('starts_at')
                    ->format(Schema::FORMAT_DATE),
                Schema::string('ends_at')
                    ->format(Schema::FORMAT_DATE)
            );
    }
}
