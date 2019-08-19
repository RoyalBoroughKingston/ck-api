<?php

namespace App\Docs\Paths\PageFeedbacks;

use App\Docs\Operations\PageFeedbacks\IndexPageFeedbackOperation;
use App\Docs\Operations\PageFeedbacks\StorePageFeedbackOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class PageFeedbacksRootPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/page-feedbacks')
            ->operations(
                IndexPageFeedbackOperation::create(),
                StorePageFeedbackOperation::create()
            );
    }
}
