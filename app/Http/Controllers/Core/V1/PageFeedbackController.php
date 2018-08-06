<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\PageFeedback\PageFeedbackCreated;
use App\Events\PageFeedback\PageFeedbackRead;
use App\Events\PageFeedback\PageFeedbacksListed;
use App\Http\Requests\PageFeedback\IndexRequest;
use App\Http\Requests\PageFeedback\ShowRequest;
use App\Http\Requests\PageFeedback\StoreRequest;
use App\Http\Resources\PageFeedbackResource;
use App\Models\PageFeedback;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
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
     * @param \App\Http\Requests\PageFeedback\StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $pageFeedback = PageFeedback::create([
                'url' => $request->url,
                'feedback' => $request->feedback,
            ]);

            event(new PageFeedbackCreated($request, $pageFeedback));

            return new PageFeedbackResource($pageFeedback);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\PageFeedback\ShowRequest $request
     * @param  \App\Models\PageFeedback $pageFeedback
     * @return \App\Http\Resources\PageFeedbackResource
     */
    public function show(ShowRequest $request, PageFeedback $pageFeedback)
    {
        event(new PageFeedbackRead($request, $pageFeedback));

        return new PageFeedbackResource($pageFeedback);
    }
}
