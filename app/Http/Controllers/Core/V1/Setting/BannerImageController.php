<?php

namespace App\Http\Controllers\Core\V1\Setting;

use App\Events\EndpointHit;
use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\BannerImage\ShowRequest;
use App\Models\File;
use App\Models\Setting;
use Illuminate\Http\Response;

class BannerImageController extends Controller
{
    /**
     * @param \App\Http\Requests\Setting\BannerImage\ShowRequest $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(ShowRequest $request)
    {
        event(EndpointHit::onRead($request, 'Viewed banner image'));

        $buttonImageFileId = Setting::cms()->value['frontend']['banner']['image_file_id'];

        abort_if(
            $buttonImageFileId === null,
            Response::HTTP_NOT_FOUND,
            'No banner image has been provided.'
        );

        // Get the logo file associated.
        /** @var \App\Models\File $file */
        $file = File::query()->findOrFail($buttonImageFileId);

        // Return the file.
        return $file->resizedVersion($request->max_dimension);
    }
}
