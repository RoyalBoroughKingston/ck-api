<?php

namespace App\Http\Controllers\Core\V1\Service;

use App\Events\EndpointHit;
use App\Http\Controllers\Controller;
use App\Http\Requests\Service\Logo\ShowRequest;
use App\Models\File;
use App\Models\Service;
use App\Models\UpdateRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class GalleryItemController extends Controller
{
    /**
     * GalleryItemController the specified resource.
     *
     * @param \App\Http\Requests\Service\Logo\ShowRequest $request
     * @param \App\Models\Service $service
     * @param \App\Models\File $file
     * @return \App\Models\File|\Illuminate\Http\Response
     */
    public function __invoke(ShowRequest $request, Service $service, File $file)
    {
        // 404 if the gallery item is not for the service (or update request) specified.
        if ($request->has('update_request_id')) {
            // Get the update request.
            $updateRequest = UpdateRequest::query()
                ->serviceId($service->id)
                ->where('id', '=', $request->update_request_id)
                ->firstOrFail();

            // Abort if the update request does not contain the file ID.
            abort_if(
                !in_array(
                    $file->id,
                    Arr::pluck($updateRequest->data['gallery_items'] ?? [], 'file_id')
                ),
                Response::HTTP_NOT_FOUND
            );
        } else {
            // Abort if the service does not have this file in it's gallery.
            abort_if(
                $service->serviceGalleryItems()
                    ->where('file_id', '=', $file->id)
                    ->doesntExist(),
                Response::HTTP_NOT_FOUND
            );
        }

        event(EndpointHit::onRead($request, "Viewed gallery item for service [{$service->id}]", $service));

        // Return the file, or placeholder if the file is null.
        return $file->resizedVersion($request->max_dimension);
    }
}
