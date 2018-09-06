<?php

namespace App\Http\Controllers\Core\V1\Service;

use App\Events\EndpointHit;
use App\Http\Requests\Service\SeoImage\ShowRequest;
use App\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class SeoImageController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\Service\SeoImage\ShowRequest $request
     * @param  \App\Models\Service $service
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function __invoke(ShowRequest $request, Service $service)
    {
        event(EndpointHit::onRead($request, "Viewed SEO image for service [{$service->id}]", $service));

        return $service->seoImageFile ?? response()->make(
            Storage::disk('local')->get('/placeholders/image.png'),
            Response::HTTP_OK,
            ['Content-Type' => 'image/png']
        );
    }
}
