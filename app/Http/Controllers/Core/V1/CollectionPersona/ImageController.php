<?php

namespace App\Http\Controllers\Core\V1\CollectionPersona;

use App\Events\CollectionPersona\Image\ImageRead;
use App\Http\Requests\CollectionPersona\Image\DestroyRequest;
use App\Http\Requests\CollectionPersona\Image\ShowRequest;
use App\Http\Requests\CollectionPersona\Image\StoreRequest;
use App\Models\Collection;
use App\Models\File;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
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
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Collection $persona
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request, Collection $persona)
    {
        // TODO: If the persona already has an image then delete it.

        // TODO: Create the file record.

        // TODO: Update the persona record to point to the file.

        // TODO: Upload the file.
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\CollectionPersona\Image\ShowRequest $request
     * @param  \App\Models\Collection $persona
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function show(ShowRequest $request, Collection $persona)
    {
        event(new ImageRead($request, $persona));

        $image = File::find($persona->meta['image_file_id']);

        return $image ?? response()->make(
            Storage::disk('local')->get('/placeholders/image.png'),
            Response::HTTP_OK,
            ['Content-Type' => 'image/png']
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Collection $persona
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, Collection $persona)
    {
        //
    }
}
