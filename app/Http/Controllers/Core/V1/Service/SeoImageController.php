<?php

namespace App\Http\Controllers\Core\V1\Service;

use App\Events\EndpointHit;
use App\Http\Requests\Service\SeoImage\DestroyRequest;
use App\Http\Requests\Service\SeoImage\ShowRequest;
use App\Http\Requests\Service\SeoImage\StoreRequest;
use App\Http\Responses\UpdateRequestReceived;
use App\Models\Service;
use App\Models\File;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SeoImageController extends Controller
{
    /**
     * SeoImageController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except('show');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Service\SeoImage\StoreRequest $request
     * @param  \App\Models\Service $service
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request, Service $service)
    {
        return DB::transaction(function () use ($request, $service) {
            // Create the file record.
            $file = File::create([
                'filename' => $service->id.'.png',
                'mime_type' => 'image/png',
                'is_private' => false,
            ]);

            // Create an update request for the service.
            $service->updateRequests()->create([
                'user_id' => $request->user()->id,
                'data' => [
                    'seo_image_file_id' => $file->id,
                ]
            ]);

            // Upload the file.
            $file->uploadBase64EncodedPng($request->input('file'));

            event(EndpointHit::onCreate($request, "Created SEO image for service [{$service->id}]", $service));

            return new UpdateRequestReceived(['seo_image_file_id' => $file->id], Response::HTTP_CREATED);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\Service\SeoImage\ShowRequest $request
     * @param  \App\Models\Service $service
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function show(ShowRequest $request, Service $service)
    {
        event(EndpointHit::onRead($request, "Viewed SEO image for service [{$service->id}]", $service));

        return $service->seoImageFile ?? response()->make(
            Storage::disk('local')->get('/placeholders/image.png'),
            Response::HTTP_OK,
            ['Content-Type' => 'image/png']
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\Service\SeoImage\DestroyRequest $request
     * @param  \App\Models\Service $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, Service $service)
    {
        if ($service->seo_image_file_id === null) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return DB::transaction(function () use ($request, $service) {
            // Create an update request for the service.
            $service->updateRequests()->create([
                'user_id' => $request->user()->id,
                'data' => [
                    'seo_image_file_id' => null,
                ]
            ]);

            event(EndpointHit::onDelete($request, "Deleted SEO image for service [{$service->id}]", $service));

            return new UpdateRequestReceived(['seo_image_file_id' => null]);
        });
    }
}
