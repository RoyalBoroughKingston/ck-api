<?php

namespace App\Docs\Paths\PageFeedbacks;

use App\Docs\Operations\PageFeedbacks\IndexPageFeedbackOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class PageFeedbacksIndexPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/page-feedbacks/index')
            ->operations(
                IndexPageFeedbackOperation::create()
                    ->action(IndexPageFeedbackOperation::ACTION_POST)
                    ->description(
                        <<<'EOT'
This is an alias of `GET /page-feedbacks` which allows all the query string parameters to be passed 
as part of the request body.
EOT
                    )
            );
    }
}
