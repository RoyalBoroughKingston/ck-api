<?php

namespace App\Http\Controllers\Core\V1\Report;

use App\Events\EndpointHit;
use App\Http\Requests\Report\Download\ShowRequest;
use App\Models\Report;
use App\Http\Controllers\Controller;

class DownloadController extends Controller
{
    /**
     * DownloadController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\Report\Download\ShowRequest $request
     * @param  \App\Models\Report $report
     * @return void
     */
    public function show(ShowRequest $request, Report $report)
    {
        event(EndpointHit::onRead($request, "Downloaded file for report [{$report->id}]", $report));

        return $report->file;
    }
}
