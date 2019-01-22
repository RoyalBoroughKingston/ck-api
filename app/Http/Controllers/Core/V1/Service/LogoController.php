<?php

namespace App\Http\Controllers\Core\V1\Service;

use App\Events\EndpointHit;
use App\Http\Requests\Service\Logo\ShowRequest;
use App\Models\File;
use App\Models\Service;
use App\Http\Controllers\Controller;
use App\Models\UpdateRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class LogoController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\Service\Logo\ShowRequest $request
     * @param  \App\Models\Service $service
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function __invoke(ShowRequest $request, Service $service)
    {
        event(EndpointHit::onRead($request, "Viewed logo for service [{$service->id}]", $service));

        // Use the file from an update request if specified.
        if ($request->has('update_request_id')) {
            $updateRequest = UpdateRequest::query()
                ->serviceId($service->id)
                ->where('id', '=', $request->update_request_id)
                ->firstOrFail();

            return File::findOrFail($updateRequest->data['logo_file_id']);
        }

        return $service->logoFile ?? response()->make(
            Storage::disk('local')->get('/placeholders/service.png'),
            Response::HTTP_OK,
            ['Content-Type' => 'image/png']
        );
    }
}
