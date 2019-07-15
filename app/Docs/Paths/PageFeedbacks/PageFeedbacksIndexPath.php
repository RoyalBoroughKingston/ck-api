<?php


namespace App\Docs\Paths\PageFeedbacks;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class PageFeedbacksIndexPath extends PathItem
{
    /**
     * @param string|null $objectId
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/page-feedbacks/index')
            ->operations(
                //
            );
    }
}
