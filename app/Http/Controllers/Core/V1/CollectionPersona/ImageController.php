<?php

namespace App\Http\Controllers\Core\V1\CollectionPersona;

use App\Events\EndpointHit;
use App\Http\Requests\CollectionPersona\Image\DestroyRequest;
use App\Http\Requests\CollectionPersona\Image\ShowRequest;
use App\Http\Requests\CollectionPersona\Image\StoreRequest;
use App\Http\Responses\FileUploaded;
use App\Http\Responses\ResourceDeleted;
use App\Models\Collection;
use App\Models\File;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * ImageController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except('show');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\CollectionPersona\Image\StoreRequest $request
     * @param  \App\Models\Collection $collection
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request, Collection $collection)
    {
        return DB::transaction(function () use ($request, $collection) {
            // If the persona already has an image then delete it.
            if ($collection->meta['image_file_id']) {
                File::findOrFail($collection->meta['image_file_id'])->delete();
            }

            // Create the file record.
            $file = File::create([
                'filename' => $collection->id.'.png',
                'mime_type' => 'image/png',
                'is_private' => false,
            ]);

            // Update the persona record to point to the file.
            $meta = $collection->meta;
            $meta['image_file_id'] = $file->id;
            $collection->meta = $meta;
            $collection->save();

            // Upload the file.
            $file->uploadBase64EncodedPng($request->input('file'));

            event(EndpointHit::onCreate($request, "Created image for collection persona [{$collection->id}]", $collection));

            return new FileUploaded("persona collection's image");
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\CollectionPersona\Image\ShowRequest $request
     * @param  \App\Models\Collection $collection
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function show(ShowRequest $request, Collection $collection)
    {
        event(EndpointHit::onRead($request, "Viewed image for collection persona [{$collection->id}]", $collection));

        $image = File::find($collection->meta['image_file_id']);

        return $image ?? response()->make(
            Storage::disk('local')->get('/placeholders/image.png'),
            Response::HTTP_OK,
            ['Content-Type' => 'image/png']
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\CollectionPersona\Image\DestroyRequest $request
     * @param  \App\Models\Collection $collection
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, Collection $collection)
    {
        if ($collection->meta['image_file_id'] === null) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return DB::transaction(function () use ($request, $collection) {
            // Delete the file record.
            File::findOrFail($collection->meta['image_file_id'])->delete();

            // Update the persona record to point to the file.
            $meta = $collection->meta;
            $meta['image_file_id'] = null;
            $collection->meta = $meta;
            $collection->save();

            event(EndpointHit::onDelete($request, "Deleted image for collection persona [{$collection->id}]", $collection));

            return new ResourceDeleted("persona collection's image");
        });
    }
}
