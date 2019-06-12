<?php

namespace App\Http\Controllers\Core\V1\Service;

use App\Events\EndpointHit;
use App\Http\Controllers\Controller;
use App\Http\Requests\Service\Refresh\UpdateRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Models\ServiceRefreshToken;
use Illuminate\Support\Facades\DB;

class RefreshController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\Service\Refresh\UpdateRequest $request
     * @param \App\Models\Service $service
     * @return \Illuminate\Http\Response
     */
    public function __invoke(UpdateRequest $request, Service $service)
    {
        return DB::transaction(function () use ($request, $service) {
            // Update the last_modified_at timestamp to now.
            $service->update(['last_modified_at' => now()]);

            // Delete the token used.
            ServiceRefreshToken::query()->findOrFail($request->token)->delete();

            event(EndpointHit::onUpdate($request, "Refreshed service [{$service->id}]", $service));

            return new ServiceResource($service);
        });
    }
}
