<?php

namespace App\Http\Controllers\Core\V1\CollectionCategory;

use App\Events\EndpointHit;
use App\Http\Controllers\Controller;
use App\Http\Requests\CollectionCategory\Image\ShowRequest;
use App\Models\Collection;
use App\Models\File;

class ImageController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\CollectionCategory\Image\ShowRequest $request
     * @param \App\Models\Collection $collection
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @return \Illuminate\Http\Response
     */
    public function __invoke(ShowRequest $request, Collection $collection)
    {
        event(EndpointHit::onRead($request, "Viewed image for collection Category [{$collection->id}]", $collection));

        $image_id = $collection->meta['image_file_id'] ?? '';

        if (is_uuid($image_id)) {
            // Get the logo file associated.
            $file = File::find($image_id);
            $image = $file ? $file->resizedVersion($request->max_dimension) : null;
        }

        // Return the file, or placeholder if the file is null.
        return $image ?? Collection::categoryPlaceholderLogo($request->max_dimension);
    }
}
