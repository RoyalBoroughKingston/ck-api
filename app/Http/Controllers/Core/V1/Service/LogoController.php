<?php

namespace App\Http\Controllers\Core\V1\Service;

use App\Events\EndpointHit;
use App\Http\Requests\Service\Logo\ShowRequest;
use App\Models\Service;
use App\Http\Controllers\Controller;
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

        return $service->logoFile ?? response()->make(
            Storage::disk('local')->get('/placeholders/service.png'),
            Response::HTTP_OK,
            ['Content-Type' => 'image/png']
        );
    }
}
