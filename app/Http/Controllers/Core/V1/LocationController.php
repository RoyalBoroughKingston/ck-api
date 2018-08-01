<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\Location\LocationCreated;
use App\Events\Location\LocationDeleted;
use App\Events\Location\LocationRead;
use App\Events\Location\LocationsListed;
use App\Events\Location\LocationUpdated;
use App\Http\Requests\Location\DestroyRequest;
use App\Http\Requests\Location\IndexRequest;
use App\Http\Requests\Location\ShowRequest;
use App\Http\Requests\Location\StoreRequest;
use App\Http\Requests\Location\UpdateRequest;
use App\Http\Resources\LocationResource;
use App\Http\Responses\ResourceDeleted;
use App\Http\Responses\UpdateRequestReceived;
use App\Models\Location;
use App\Models\ServiceLocation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

class LocationController extends Controller
{
    /**
     * LocationController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\Location\IndexRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        event(new LocationsListed($request));

        $locations = QueryBuilder::for(Location::class)
            ->paginate();

        return LocationResource::collection($locations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Location\StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        return DB::transaction(function () use ($request) {
            // Create a location instance.
            $location = new Location([
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'address_line_3' => $request->address_line_3,
                'city' => $request->city,
                'county' => $request->county,
                'postcode' => $request->postcode,
                'country' => $request->country,
                'accessibility_info' => $request->accessibility_info,
            ]);

            // Persist the record to the database.
            $location->save();

            event(new LocationCreated($request, $location));

            return new LocationResource($location);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\Location\ShowRequest $request
     * @param  \App\Models\Location $location
     * @return \App\Http\Resources\LocationResource
     */
    public function show(ShowRequest $request, Location $location)
    {
        event(new LocationRead($request, $location));

        return new LocationResource($location);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Location\UpdateRequest $request
     * @param  \App\Models\Location $location
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Location $location)
    {
        return DB::transaction(function () use ($request, $location) {
            $location->updateRequests()->create([
                'user_id' => $request->user()->id,
                'data' => [
                    'address_line_1' => $request->address_line_1,
                    'address_line_2' => $request->address_line_2,
                    'address_line_3' => $request->address_line_3,
                    'city' => $request->city,
                    'county' => $request->county,
                    'postcode' => $request->postcode,
                    'country' => $request->country,
                    'accessibility_info' => $request->accessibility_info,
                ]
            ]);

            event(new LocationUpdated($request, $location));

            return new UpdateRequestReceived($request->all());
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\Location\DestroyRequest $request
     * @param  \App\Models\Location $location
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, Location $location)
    {
        return DB::transaction(function () use ($request, $location) {
            event(new LocationDeleted($request, $location));

            $location->serviceLocations->each(function (ServiceLocation $serviceLocation) {
                $serviceLocation->regularOpeningHours()->delete();
                $serviceLocation->holidayOpeningHours()->delete();
            });
            $location->serviceLocations()->delete();
            $location->updateRequests()->delete();
            $location->delete();

            return new ResourceDeleted('location');
        });
    }
}
