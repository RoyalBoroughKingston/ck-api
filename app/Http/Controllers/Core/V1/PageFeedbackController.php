<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\PageFeedback\PageFeedbacksListed;
use App\Http\Requests\PageFeedback\IndexRequest;
use App\Http\Requests\PageFeedback\ShowRequest;
use App\Http\Requests\PageFeedback\StoreRequest;
use App\Http\Resources\PageFeedbackResource;
use App\Models\PageFeedback;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;

class PageFeedbackController extends Controller
{
    /**
     * PageFeedbackController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except('store');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\PageFeedback\IndexRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $pageFeedbacks = QueryBuilder::for(PageFeedback::class)
            ->paginate();

        event(new PageFeedbacksListed($request));

        return PageFeedbackResource::collection($pageFeedbacks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PageFeedback  $pageFeedback
     * @return \Illuminate\Http\Response
     */
    public function show(ShowRequest $request, PageFeedback $pageFeedback)
    {
        //
    }
}
