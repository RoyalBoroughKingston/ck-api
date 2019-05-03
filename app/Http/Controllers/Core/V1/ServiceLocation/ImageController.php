<?php

namespace App\Http\Controllers\Core\V1\ServiceLocation;

use App\Events\EndpointHit;
use App\Http\Requests\ServiceLocation\Image\ShowRequest;
use App\Models\File;
use App\Http\Controllers\Controller;
use App\Models\ServiceLocation;
use App\Models\UpdateRequest;

class ImageController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\ServiceLocation\Image\ShowRequest $request
     * @param \App\Models\ServiceLocation $serviceLocation
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function __invoke(ShowRequest $request, ServiceLocation $serviceLocation)
    {
        event(EndpointHit::onRead($request, "Viewed image for service location [{$serviceLocation->id}]", $serviceLocation));

        // Get the image file associated.
        $file = $serviceLocation->imageFile;

        // Use the file from an update request instead, if specified.
        if ($request->has('update_request_id')) {
            $imageFileId = UpdateRequest::query()
                ->serviceLocationId($serviceLocation->id)
                ->where('id', '=', $request->update_request_id)
                ->firstOrFail()
                ->data['image_file_id'];

            /** @var \App\Models\File $file */
            $file = File::findOrFail($imageFileId);
        }

        // Return the file, or placeholder if the file is null.
        return optional($file)->resizedVersion($request->max_dimension)
            ?? ServiceLocation::placeholderImage($request->max_dimension);
    }
}
