<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatusUpdate\IndexRequest;
use App\Http\Resources\StatusUpdateResource;
use App\Models\Referral;
use App\Models\StatusUpdate;
use Spatie\QueryBuilder\QueryBuilder;

class StatusUpdateController extends Controller
{
    /**
     * StatusUpdateController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\StatusUpdate\IndexRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $referral = Referral::findOrFail($request->filter['referral_id']);
        $baseQuery = StatusUpdate::where('referral_id', $referral->id);
        $statusUpdates = QueryBuilder::for($baseQuery)->paginate();

        event(EndpointHit::onRead($request, "Viewed all status updates for referral [{$referral->id}]", $referral));

        return StatusUpdateResource::collection($statusUpdates);
    }
}