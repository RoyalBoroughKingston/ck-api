<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Requests\ServiceLocation\DestroyRequest;
use App\Http\Requests\ServiceLocation\IndexRequest;
use App\Http\Requests\ServiceLocation\ShowRequest;
use App\Http\Requests\ServiceLocation\StoreRequest;
use App\Http\Requests\ServiceLocation\UpdateRequest;
use App\Http\Resources\ServiceLocationResource;
use App\Models\ServiceLocation;
use App\Http\Controllers\Controller;
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
     * @param  \App\Models\ServiceLocation  $serviceLocation
     * @return \Illuminate\Http\Response
     */
    public function show(ShowRequest $request, ServiceLocation $serviceLocation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ServiceLocation  $serviceLocation
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, ServiceLocation $serviceLocation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ServiceLocation  $serviceLocation
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, ServiceLocation $serviceLocation)
    {
        //
    }
}
