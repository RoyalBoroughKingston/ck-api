<?php

namespace App\Docs\Paths\Reports;

use App\Docs\Operations\Reports\IndexReportOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class ReportsIndexPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/reports/index')
            ->operations(
                IndexReportOperation::create()
                    ->action(IndexReportOperation::ACTION_POST)
                    ->description(
                        <<<'EOT'
This is an alias of `GET /reports` which allows all the query string parameters to be passed as part 
of the request body.
EOT
                    )
            );
    }
}
