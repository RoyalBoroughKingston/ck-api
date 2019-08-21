<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Controllers\Controller;
use App\Http\Filters\UpdateRequest\EntryFilter;
use App\Http\Requests\UpdateRequest\DestroyRequest;
use App\Http\Requests\UpdateRequest\IndexRequest;
use App\Http\Requests\UpdateRequest\ShowRequest;
use App\Http\Resources\UpdateRequestResource;
use App\Http\Responses\ResourceDeleted;
use App\Models\UpdateRequest;
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
        $this->middleware('throttle:60,1');
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
        $baseQuery = UpdateRequest::query()
            ->select('*')
            ->withEntry()
            ->pending();

        $updateRequests = QueryBuilder::for($baseQuery)
            ->allowedFilters([
                Filter::exact('id'),
                Filter::scope('service_id'),
                Filter::scope('service_location_id'),
                Filter::scope('location_id'),
                Filter::scope('organisation_id'),
                Filter::custom('entry', EntryFilter::class),
            ])
            ->allowedIncludes(['user'])
            ->allowedSorts([
                'entry',
                'created_at',
            ])
            ->defaultSort('-created_at')
            ->paginate(per_page($request->per_page));

        event(EndpointHit::onRead($request, 'Viewed all update requests'));

        return UpdateRequestResource::collection($updateRequests);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\UpdateRequest\ShowRequest $request
     * @param \App\Models\UpdateRequest $updateRequest
     * @return \App\Http\Resources\UpdateRequestResource
     */
    public function show(ShowRequest $request, UpdateRequest $updateRequest)
    {
        $baseQuery = UpdateRequest::query()
            ->select('*')
            ->withEntry()
            ->where('id', $updateRequest->id);

        $updateRequest = QueryBuilder::for($baseQuery)
            ->firstOrFail();

        event(EndpointHit::onRead($request, "Viewed update request [{$updateRequest->id}]", $updateRequest));

        return new UpdateRequestResource($updateRequest);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\UpdateRequest\DestroyRequest $request
     * @param \App\Models\UpdateRequest $updateRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, UpdateRequest $updateRequest)
    {
        return DB::transaction(function () use ($request, $updateRequest) {
            event(EndpointHit::onDelete($request, "Deleted update request [{$updateRequest->id}]", $updateRequest));

            $updateRequest->delete();

            return new ResourceDeleted('update request');
        });
    }
}
