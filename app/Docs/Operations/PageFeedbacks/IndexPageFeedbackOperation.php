<?php

namespace App\Docs\Operations\PageFeedbacks;

use App\Docs\Parameters\FilterIdParameter;
use App\Docs\Parameters\FilterParameter;
use App\Docs\Parameters\PageParameter;
use App\Docs\Parameters\PerPageParameter;
use App\Docs\Parameters\SortParameter;
use App\Docs\Schemas\PageFeedback\PageFeedbackSchema;
use App\Docs\Schemas\PaginationSchema;
use App\Docs\Tags\PageFeedbacksTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class IndexPageFeedbackOperation extends Operation
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
            ->summary('List all the page feedbacks')
            ->description('**Permission:** `Global Admin`')
            ->parameters(
                PageParameter::create(),
                PerPageParameter::create(),
                FilterIdParameter::create(),
                FilterParameter::create(null, 'url')
                    ->description('URL to filter by')
                    ->schema(Schema::string()),
                SortParameter::create(null, ['url', 'created_at'], '-created_at')
            )
            ->responses(
                Response::ok()->content(
                    MediaType::json()->schema(
                        PaginationSchema::create(null, PageFeedbackSchema::create())
                    )
                )
            );
    }
}
