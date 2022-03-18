<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Controllers\Controller;
use App\Http\Requests\PageFeedback\IndexRequest;
use App\Http\Requests\PageFeedback\ShowRequest;
use App\Http\Requests\PageFeedback\StoreRequest;
use App\Http\Resources\PageFeedbackResource;
use App\Models\PageFeedback;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
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
        $baseQuery = PageFeedback::query();

        $pageFeedbacks = QueryBuilder::for($baseQuery)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                'url',
            ])
            ->allowedSorts([
                'url',
                'created_at',
            ])
            ->defaultSort('-created_at')
            ->paginate(per_page($request->per_page));

        event(EndpointHit::onRead($request, 'Viewed all page feedbacks'));

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
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'consented_at' => Date::now(),
            ]);

            event(EndpointHit::onCreate($request, "Created page feedback [{$pageFeedback->id}]", $pageFeedback));

            return new PageFeedbackResource($pageFeedback);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\PageFeedback\ShowRequest $request
     * @param \App\Models\PageFeedback $pageFeedback
     * @return \App\Http\Resources\PageFeedbackResource
     */
    public function show(ShowRequest $request, PageFeedback $pageFeedback)
    {
        $baseQuery = PageFeedback::query()
            ->where('id', $pageFeedback->id);

        $pageFeedback = QueryBuilder::for($baseQuery)
            ->firstOrFail();

        event(EndpointHit::onRead($request, "Viewed page feedback [{$pageFeedback->id}]", $pageFeedback));

        return new PageFeedbackResource($pageFeedback);
    }
}
