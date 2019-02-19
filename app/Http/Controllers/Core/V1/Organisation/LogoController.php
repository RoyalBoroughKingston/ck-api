<?php

namespace App\Http\Controllers\Core\V1\Organisation;

use App\Events\EndpointHit;
use App\Http\Requests\Organisation\Logo\ShowRequest;
use App\Models\File;
use App\Models\Organisation;
use App\Http\Controllers\Controller;
use App\Models\UpdateRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class LogoController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\Organisation\Logo\ShowRequest $request
     * @param  \App\Models\Organisation $organisation
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function __invoke(ShowRequest $request, Organisation $organisation)
    {
        event(EndpointHit::onRead($request, "Viewed logo for organisation [{$organisation->id}]", $organisation));

        // Use the file from an update request if specified.
        if ($request->has('update_request_id')) {
            $logoFileId = UpdateRequest::query()
                ->organisationId($organisation->id)
                ->where('id', '=', $request->update_request_id)
                ->firstOrFail()
                ->data['logo_file_id'];
            $file = File::find($logoFileId);

            return $file ?? $this->placeholder();
        }

        return $organisation->logoFile ?? $this->placeholder();
    }

    /**
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function placeholder(): Response
    {
        return response()->make(
            Storage::disk('local')->get('/placeholders/organisation.png'),
            Response::HTTP_OK,
            ['Content-Type' => 'image/png']
        );
    }
}
