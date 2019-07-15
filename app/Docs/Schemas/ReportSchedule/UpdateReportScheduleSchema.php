<?php

namespace App\Docs\Schemas\ReportSchedule;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateReportScheduleSchema extends Schema
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->type(static::FORMAT_UUID)
            ->required('report_type', 'repeat_type')
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
                Schema::string('repeat_type')
                    ->enum('weekly', 'monthly')
            );
    }
}
