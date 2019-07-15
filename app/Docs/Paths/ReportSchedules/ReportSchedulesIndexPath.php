<?php


namespace App\Docs\Paths\ReportSchedules;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class ReportSchedulesIndexPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/report-schedules/index')
            ->operations(
                //
            );
    }
}
