<?php

namespace App\Http\Controllers\Core\V1\CollectionPersona;

use App\Events\EndpointHit;
use App\Http\Requests\CollectionPersona\Image\ShowRequest;
use App\Models\Collection;
use App\Models\File;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\CollectionPersona\Image\ShowRequest $request
     * @param  \App\Models\Collection $collection
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function __invoke(ShowRequest $request, Collection $collection)
    {
        event(EndpointHit::onRead($request, "Viewed image for collection persona [{$collection->id}]", $collection));

        $image = File::find($collection->meta['image_file_id']);

        return $image ?? response()->make(
            Storage::disk('local')->get('/placeholders/image.png'),
            Response::HTTP_OK,
            ['Content-Type' => 'image/png']
        );
    }
}
