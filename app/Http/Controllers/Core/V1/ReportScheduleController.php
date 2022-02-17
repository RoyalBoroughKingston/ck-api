<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReportSchedule\DestroyRequest;
use App\Http\Requests\ReportSchedule\IndexRequest;
use App\Http\Requests\ReportSchedule\ShowRequest;
use App\Http\Requests\ReportSchedule\StoreRequest;
use App\Http\Requests\ReportSchedule\UpdateRequest;
use App\Http\Resources\ReportScheduleResource;
use App\Http\Responses\ResourceDeleted;
use App\Models\ReportSchedule;
use App\Models\ReportType;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
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
        $baseQuery = ReportSchedule::query()
            ->orderByDesc('created_at');

        $reportSchedules = QueryBuilder::for($baseQuery)
            ->allowedFilters([
                AllowedFilter::exact('id'),
            ])
            ->paginate(per_page($request->per_page));

        event(EndpointHit::onRead($request, 'Viewed all report schedules'));

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

            event(EndpointHit::onCreate($request, "Created report schedule [{$reportSchedule->id}]", $reportSchedule));

            return new ReportScheduleResource($reportSchedule);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\ReportSchedule\ShowRequest $request
     * @param \App\Models\ReportSchedule $reportSchedule
     * @return \App\Http\Resources\ReportScheduleResource
     */
    public function show(ShowRequest $request, ReportSchedule $reportSchedule)
    {
        $baseQuery = ReportSchedule::query()
            ->where('id', $reportSchedule->id);

        $reportSchedule = QueryBuilder::for($baseQuery)
            ->firstOrFail();

        event(EndpointHit::onRead($request, "Viewed report schedule [{$reportSchedule->id}]", $reportSchedule));

        return new ReportScheduleResource($reportSchedule);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\ReportSchedule\UpdateRequest $request
     * @param \App\Models\ReportSchedule $reportSchedule
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, ReportSchedule $reportSchedule)
    {
        return DB::transaction(function () use ($request, $reportSchedule) {
            $reportSchedule->update([
                'report_type_id' => ReportType::where('name', $request->report_type)->firstOrFail()->id,
                'repeat_type' => $request->repeat_type,
            ]);

            event(EndpointHit::onUpdate($request, "Updated report schedule [{$reportSchedule->id}]", $reportSchedule));

            return new ReportScheduleResource($reportSchedule);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\ReportSchedule\DestroyRequest $request
     * @param \App\Models\ReportSchedule $reportSchedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, ReportSchedule $reportSchedule)
    {
        return DB::transaction(function () use ($request, $reportSchedule) {
            event(EndpointHit::onDelete($request, "Deleted report schedule [{$reportSchedule->id}]", $reportSchedule));

            $reportSchedule->delete();

            return new ResourceDeleted('report schedule');
        });
    }
}
