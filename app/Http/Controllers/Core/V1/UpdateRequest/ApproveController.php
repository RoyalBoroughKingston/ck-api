<?php

namespace App\Http\Controllers\Core\V1\UpdateRequest;

use App\Events\EndpointHit;
use App\Http\Requests\UpdateRequest\Approve\UpdateRequest as Request;
use App\Http\Resources\UpdateRequestResource;
use App\Models\UpdateRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ApproveController extends Controller
{
    /**
     * ApproveController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateRequest\Approve\UpdateRequest $request
     * @param  \App\Models\UpdateRequest $updateRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UpdateRequest $updateRequest)
    {
        return DB::transaction(function () use ($request, $updateRequest) {
            $updateRequest->apply();

            event(EndpointHit::onUpdate($request, "Approved update request [{$updateRequest->id}]", $updateRequest));

            return new UpdateRequestResource($updateRequest);
        });
    }
}
