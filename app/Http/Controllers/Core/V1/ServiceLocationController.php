<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Requests\ServiceLocation\DestroyRequest;
use App\Http\Requests\ServiceLocation\IndexRequest;
use App\Http\Requests\ServiceLocation\ShowRequest;
use App\Http\Requests\ServiceLocation\StoreRequest;
use App\Http\Requests\ServiceLocation\UpdateRequest;
use App\Http\Resources\ServiceLocationResource;
use App\Http\Responses\ResourceDeleted;
use App\Http\Responses\UpdateRequestReceived;
use App\Models\RegularOpeningHour;
use App\Models\ServiceLocation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class ServiceLocationController extends Controller
{
    /**
     * ServiceLocationController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\ServiceLocation\IndexRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $baseQuery = ServiceLocation::query()
            ->with('regularOpeningHours', 'holidayOpeningHours');

        $serviceLocations = QueryBuilder::for($baseQuery)
            ->allowedFilters(Filter::exact('service_id'))
            ->paginate();

        event(EndpointHit::onRead($request, 'Viewed all service locations'));

        return ServiceLocationResource::collection($serviceLocations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\ServiceLocation\StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        return DB::transaction(function () use ($request) {
            // Create the service location.
            $serviceLocation = ServiceLocation::create([
                'service_id' => $request->service_id,
                'location_id' => $request->location_id,
                'name' => $request->name,
            ]);

            // Attach the regular opening hours.
            foreach ($request->regular_opening_hours as $regularOpeningHour) {
                $serviceLocation->regularOpeningHours()->create([
                    'frequency' => $regularOpeningHour['frequency'],
                    'weekday' => ($regularOpeningHour['frequency'] === RegularOpeningHour::FREQUENCY_WEEKLY)
                        ? $regularOpeningHour['weekday']
                        : null,
                    'day_of_month' => ($regularOpeningHour['frequency'] === RegularOpeningHour::FREQUENCY_MONTHLY)
                        ? $regularOpeningHour['day_of_month']
                        : null,
                    'occurrence_of_month' => ($regularOpeningHour['frequency'] === RegularOpeningHour::FREQUENCY_NTH_OCCURRENCE_OF_MONTH)
                        ? $regularOpeningHour['occurrence_of_month']
                        : null,
                    'starts_at' => ($regularOpeningHour['frequency'] === RegularOpeningHour::FREQUENCY_FORTNIGHTLY)
                        ? $regularOpeningHour['starts_at']
                        : null,
                    'opens_at' => $regularOpeningHour['opens_at'],
                    'closes_at' => $regularOpeningHour['closes_at'],
                ]);
            }

            // Attach the holiday opening hours.
            foreach ($request->holiday_opening_hours as $holidayOpeningHour) {
                $serviceLocation->holidayOpeningHours()->create([
                    'is_closed' => $holidayOpeningHour['is_closed'],
                    'starts_at' => $holidayOpeningHour['starts_at'],
                    'ends_at' => $holidayOpeningHour['ends_at'],
                    'opens_at' => $holidayOpeningHour['opens_at'],
                    'closes_at' => $holidayOpeningHour['closes_at'],
                ]);
            }

            event(EndpointHit::onCreate($request, "Created service location [{$serviceLocation->id}]", $serviceLocation));

            return new ServiceLocationResource($serviceLocation);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\ServiceLocation\ShowRequest $request
     * @param  \App\Models\ServiceLocation $serviceLocation
     * @return \App\Http\Resources\ServiceLocationResource
     */
    public function show(ShowRequest $request, ServiceLocation $serviceLocation)
    {
        event(EndpointHit::onRead($request, "Viewed service location [{$serviceLocation->id}]", $serviceLocation));

        return new ServiceLocationResource($serviceLocation);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\ServiceLocation\UpdateRequest $request
     * @param  \App\Models\ServiceLocation $serviceLocation
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, ServiceLocation $serviceLocation)
    {
        return DB::transaction(function() use ($request, $serviceLocation) {
            // Initialise the data array.
            $data = [
                'name' => $request->name,
                'regular_opening_hours' => [],
                'holiday_opening_hours' => [],
            ];

            // Loop through each regular opening hour to normalise and then append to the array.
            foreach ($request->regular_opening_hours as $regularOpeningHour) {
                $data['regular_opening_hours'][] = array_filter_null([
                    'frequency' => $regularOpeningHour['frequency'],
                    'weekday' => ($regularOpeningHour['frequency'] === RegularOpeningHour::FREQUENCY_WEEKLY)
                        ? $regularOpeningHour['weekday']
                        : null,
                    'day_of_month' => ($regularOpeningHour['frequency'] === RegularOpeningHour::FREQUENCY_MONTHLY)
                        ? $regularOpeningHour['day_of_month']
                        : null,
                    'occurrence_of_month' => ($regularOpeningHour['frequency'] === RegularOpeningHour::FREQUENCY_NTH_OCCURRENCE_OF_MONTH)
                        ? $regularOpeningHour['occurrence_of_month']
                        : null,
                    'starts_at' => ($regularOpeningHour['frequency'] === RegularOpeningHour::FREQUENCY_FORTNIGHTLY)
                        ? $regularOpeningHour['starts_at']
                        : null,
                    'opens_at' => $regularOpeningHour['opens_at'],
                    'closes_at' => $regularOpeningHour['closes_at'],
                ]);
            }

            // Loop through each holiday opening hour to normalise and then append to the array.
            foreach ($request->holiday_opening_hours as $holidayOpeningHour) {
                $data['holiday_opening_hours'][] = [
                    'is_closed' => $holidayOpeningHour['is_closed'],
                    'starts_at' => $holidayOpeningHour['starts_at'],
                    'ends_at' => $holidayOpeningHour['ends_at'],
                    'opens_at' => $holidayOpeningHour['opens_at'],
                    'closes_at' => $holidayOpeningHour['closes_at'],
                ];
            }

            $serviceLocation->updateRequests()->create([
                'user_id' => $request->user()->id,
                'data' => $data,
            ]);

            event(EndpointHit::onUpdate($request, "Updated service location [{$serviceLocation->id}]", $serviceLocation));

            return new UpdateRequestReceived($data);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\ServiceLocation\DestroyRequest $request
     * @param  \App\Models\ServiceLocation $serviceLocation
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, ServiceLocation $serviceLocation)
    {
        return DB::transaction(function () use ($request, $serviceLocation) {
            event(EndpointHit::onDelete($request, "Deleted service location [{$serviceLocation->id}]", $serviceLocation));

            $serviceLocation->delete();

            return new ResourceDeleted('service location');
        });
    }
}
