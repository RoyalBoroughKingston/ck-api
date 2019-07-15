<?php

namespace App\Docs\Schemas\Report;

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
                    ->format(Schema::FORMAT_UUID)
                    ->example('38e06e93-79b2-4c38-85bf-7749ebc7044b'),
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
