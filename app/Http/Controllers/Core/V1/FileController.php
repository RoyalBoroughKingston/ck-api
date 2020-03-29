<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Controllers\Controller;
use App\Http\Requests\File\StoreRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use Illuminate\Support\Facades\DB;

class FileController extends Controller
{
    /**
     * FileController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\File\StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        return DB::transaction(function () use ($request) {
            /** @var \App\Models\File $file */
            $file = File::create([
                'filename' => uuid() . File::extensionFromMime($request->mime_type),
                'mime_type' => $request->mime_type,
                'meta' => [
                    'type' => File::META_TYPE_PENDING_ASSIGNMENT,
                ],
                'is_private' => $request->is_private,
            ]);

            $file->uploadBase64EncodedFile($request->file);

            event(EndpointHit::onCreate($request, "Created file [{$file->id}]", $file));

            return new FileResource($file);
        });
    }
}
