<?php

namespace App\Models;

use App\Models\Mutators\FileMutators;
use App\Models\Relationships\FileRelationships;
use App\Models\Scopes\FileScopes;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class File extends Model implements Responsable
{
    use FileMutators;
    use FileRelationships;
    use FileScopes;

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request)
    {
        $content = Storage::cloud()->get('files/' . $this->id);

        return response()->make($content, Response::HTTP_OK, [
            'Content-Type' => $this->mime_type,
        ]);
    }

    /**
     * @param string $content
     */
    public function upload(string $content)
    {
        Storage::cloud()->put('/files/' . $this->id, $content);
    }

    /**
     * @param string $content
     */
    public function uploadBase64EncodedPng(string $content)
    {
        list($type, $data) = explode(';', $content);
        list(, $data) = explode(',', $data);
        $data = base64_decode($data);

        $this->upload($data);
    }
}
