<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\ReportSchedule\ReportScheduleCreated;
use App\Events\ReportSchedule\ReportSchedulesListed;
use App\Http\Requests\ReportSchedule\DestroyRequest;
use App\Http\Requests\ReportSchedule\IndexRequest;
use App\Http\Requests\ReportSchedule\ShowRequest;
use App\Http\Requests\ReportSchedule\StoreRequest;
use App\Http\Requests\ReportSchedule\UpdateRequest;
use App\Http\Resources\ReportScheduleResource;
use App\Models\ReportSchedule;
use App\Http\Controllers\Controller;
use App\Models\ReportType;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

class ReportScheduleController extends Controller
{
    /**
     * ReportScheduleController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\ReportSchedule\IndexRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $baseQuery = ReportSchedule::query();
        $reportSchedules = QueryBuilder::for($baseQuery)->paginate();

        event(new ReportSchedulesListed($request));

        return ReportScheduleResource::collection($reportSchedules);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\ReportSchedule\StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $reportSchedule = ReportSchedule::create([
                'report_type_id' => ReportType::where('name', $request->report_type)->firstOrFail()->id,
                'repeat_type' => $request->repeat_type,
            ]);

            event(new ReportScheduleCreated($request, $reportSchedule));

            return new ReportScheduleResource($reportSchedule);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ReportSchedule $reportSchedule
     * @return \Illuminate\Http\Response
     */
    public function show(ShowRequest $request, ReportSchedule $reportSchedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\ReportSchedule $reportSchedule
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, ReportSchedule $reportSchedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ReportSchedule $reportSchedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, ReportSchedule $reportSchedule)
    {
        //
    }
}
