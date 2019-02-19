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

    const META_TYPE_RESIZED_IMAGE = 'resized_image';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_private' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request)
    {
        return response()->make($this->getContent(), Response::HTTP_OK, [
            'Content-Type' => $this->mime_type,
            'Content-Disposition' => sprintf('inline; filename="%s"', $this->filename),
        ]);
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return Storage::cloud()->get($this->path());
    }

    /**
     * @return string
     */
    public function path(): string
    {
        $directory = $this->is_private ? 'files/private' : 'files/public';

        return "/{$directory}/{$this->id}-{$this->filename}";
    }

    /**
     * @return string
     */
    protected function visibility(): string
    {
        return $this->is_private ? 'private' : 'public';
    }

    /**
     * @param string $content
     * @return \App\Models\File
     */
    public function upload(string $content): File
    {
        Storage::cloud()->put($this->path(), $content, $this->visibility());

        return $this;
    }

    /**
     * @return string
     */
    public function url(): string
    {
        return Storage::cloud()->url($this->path());
    }

    /**
     * Deletes the file from disk.
     */
    public function deleteFromDisk()
    {
        Storage::cloud()->delete($this->path());
    }

    /**
     * @param string $content
     * @return \App\Models\File
     */
    public function uploadBase64EncodedPng(string $content): File
    {
        list(, $data) = explode(';', $content);
        list(, $data) = explode(',', $data);
        $data = base64_decode($data);

        return $this->upload($data);
    }

    /**
     * @param int $maxDimension
     * @return \App\Models\File|null
     */
    public function resizedVersion(int $maxDimension): ?self
    {
        return static::query()
            ->whereRaw('`meta`->>"$.type" = ?', [static::META_TYPE_RESIZED_IMAGE])
            ->whereRaw('`meta`->>"$.data.file_id" = ?', [$this->id])
            ->whereRaw('`meta`->>"$.data.max_dimension" = ?', [$maxDimension])
            ->first();
    }
}
