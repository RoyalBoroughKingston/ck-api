<?php

namespace App\Docs\Operations\PageFeedbacks;

use App\Docs\Schemas\PageFeedback\PageFeedbackSchema;
use App\Docs\Schemas\ResourceSchema;
use App\Docs\Tags\PageFeedbacksTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class ShowPageFeedbackOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->action(static::ACTION_GET)
            ->tags(PageFeedbacksTag::create())
            ->summary('Get a specific page feedback')
            ->description('**Permission:** `Global Admin`')
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        ResourceSchema::create(null, PageFeedbackSchema::create())
                    )
                )
            );
    }
}
