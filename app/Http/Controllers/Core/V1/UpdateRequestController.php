<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Requests\UpdateRequest\DestroyRequest;
use App\Http\Requests\UpdateRequest\IndexRequest;
use App\Http\Requests\UpdateRequest\ShowRequest;
use App\Http\Resources\UpdateRequestResource;
use App\Http\Responses\ResourceDeleted;
use App\Models\UpdateRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class UpdateRequestController extends Controller
{
    /**
     * UpdateRequestController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\UpdateRequest\IndexRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $baseQuery = UpdateRequest::query();
        $updateRequests = QueryBuilder::for($baseQuery)
            ->allowedFilters([
                Filter::scope('service_id'),
                Filter::scope('service_location_id'),
                Filter::scope('location_id'),
                Filter::scope('organisation_id'),
            ])
            ->paginate();

        event(EndpointHit::onRead($request, 'Viewed all update requests'));

        return UpdateRequestResource::collection($updateRequests);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\UpdateRequest\ShowRequest $request
     * @param  \App\Models\UpdateRequest $updateRequest
     * @return \App\Http\Resources\UpdateRequestResource
     */
    public function show(ShowRequest $request, UpdateRequest $updateRequest)
    {
        event(EndpointHit::onRead($request, "Viewed update request [{$updateRequest->id}]", $updateRequest));

        return new UpdateRequestResource($updateRequest);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\UpdateRequest\DestroyRequest $request
     * @param  \App\Models\UpdateRequest $updateRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, UpdateRequest $updateRequest)
    {
        return DB::transaction(function () use ($request, $updateRequest) {
            event(EndpointHit::onRead($request, "Deleted update request [{$updateRequest->id}]", $updateRequest));

            $updateRequest->delete();

            return new ResourceDeleted('update request');
        });
    }
}
