<?php

namespace App\Docs\Paths\PageFeedbacks;

use App\Docs\Operations\PageFeedbacks\ShowPageFeedbackOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

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
            ->operations(
                ShowPageFeedbackOperation::create()
            );
    }
}
