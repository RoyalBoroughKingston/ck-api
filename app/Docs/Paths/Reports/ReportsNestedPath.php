<?php

namespace App\Docs\Paths\Reports;

use App\Docs\Operations\Reports\DestroyReportOperation;
use App\Docs\Operations\Reports\ShowReportOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ReportsNestedPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/reports/{report}')
            ->parameters(
                Parameter::path()
                    ->name('report')
                    ->description('The ID of the report')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID))
            )
            ->operations(
                ShowReportOperation::create(),
                DestroyReportOperation::create()
            );
    }
}
