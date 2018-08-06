<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Requests\Report\DestroyRequest;
use App\Http\Requests\Report\IndexRequest;
use App\Http\Requests\Report\ShowRequest;
use App\Http\Requests\Report\StoreRequest;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;

class ReportController extends Controller
{
    /**
     * ReportController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\Report\IndexRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $baseQuery = Report::query();

        $reports = QueryBuilder::for($baseQuery)->paginate();

        event(EndpointHit::onRead($request, 'Viewed all reports'));

        return ReportResource::collection($reports);
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
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function show(ShowRequest $request, Report $report)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, Report $report)
    {
        //
    }
}
