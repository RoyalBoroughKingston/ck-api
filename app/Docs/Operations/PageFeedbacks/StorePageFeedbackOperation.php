<?php

namespace App\Docs\Operations\PageFeedbacks;

use App\Docs\Schemas\PageFeedback\PageFeedbackSchema;
use App\Docs\Schemas\PageFeedback\StorePageFeedbackSchema;
use App\Docs\Schemas\ResourceSchema;
use App\Docs\Tags\PageFeedbacksTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class StorePageFeedbackOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->action(static::ACTION_POST)
            ->tags(PageFeedbacksTag::create())
            ->summary('Create a page feedback')
            ->description('**Permission:** `Open`')
            ->noSecurity()
            ->requestBody(
                RequestBody::create()
                    ->required()
                    ->content(
                        MediaType::json()->schema(StorePageFeedbackSchema::create())
                    )
            )
            ->responses(
                Response::created()->content(
                    MediaType::json()->schema(
                        ResourceSchema::create(null, PageFeedbackSchema::create())
                    )
                )
            );
    }
}
