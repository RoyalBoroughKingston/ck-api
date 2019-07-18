<?php

namespace App\Docs\Paths\PageFeedbacks;

use App\Docs\Operations\PageFeedbacks\ShowPageFeedbackOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class PageFeedbacksNestedPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/page-feedbacks/{page_feedback}')
            ->parameters(
                Parameter::path()
                    ->name('page_feedback')
                    ->description('The ID of the page feedback')
                    ->required()
                    ->schema(Schema::string()->format(Schema::FORMAT_UUID))
            )
            ->operations(
                ShowPageFeedbackOperation::create()
            );
    }
}
