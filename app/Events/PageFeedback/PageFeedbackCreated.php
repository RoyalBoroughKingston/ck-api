<?php

namespace App\Events\PageFeedback;

use App\Events\EndpointHit;
use App\Models\Audit;
use App\Models\PageFeedback;
use Illuminate\Http\Request;

class PageFeedbackCreated extends EndpointHit
{
    /**
     * Create a new event instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\PageFeedback $pageFeedback
     */
    public function __construct(Request $request, PageFeedback $pageFeedback)
    {
        parent::__construct($request);

        $this->action = Audit::ACTION_CREATE;
        $this->description = "Created page feedback [{$pageFeedback->id}]";
    }
}