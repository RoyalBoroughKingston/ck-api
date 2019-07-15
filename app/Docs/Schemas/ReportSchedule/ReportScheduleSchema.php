<?php

namespace App\Docs\Schemas\ReportSchedule;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ReportScheduleSchema extends Schema
{
    /**
     * @param string|null $objectId
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->type(static::FORMAT_UUID)
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
                Schema::string('repeat_type')
                    ->enum('weekly', 'monthly'),
                Schema::string('created_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable(),
                Schema::string('updated_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->nullable()
            );
    }
}
