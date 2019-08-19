<?php

namespace App\Models;

use App\ImageTools\Resizer;
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

    const MIME_TYPE_PNG = 'image/png';
    const MIME_TYPE_TXT = 'text/plain';

    const META_TYPE_RESIZED_IMAGE = 'resized_image';
    const META_TYPE_PENDING_ASSIGNMENT = 'pending_assignment';

    const META_PLACEHOLDER_FOR_ORGANISATION = 'organisation';
    const META_PLACEHOLDER_FOR_SERVICE = 'service';
    const META_PLACEHOLDER_FOR_COLLECTION_PERSONA = 'collection_persona';
    const META_PLACEHOLDER_FOR_LOCATION= 'location';
    const META_PLACEHOLDER_FOR_SERVICE_LOCATION= 'service_location';

    const PEDNING_ASSIGNMENT_AUTO_DELETE_DAYS = 1;

    const WITH_PERIOD = true;
    const WITHOUT_PERIOD = false;

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
     * @param \Illuminate\Http\Request $request
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
    public function uploadBase64EncodedFile(string $content): File
    {
        $data = explode(',', $content);
        $data = base64_decode(end($data));

        return $this->upload($data);
    }

    /**
     * @deprecated you should now use the uploadBase64EncodedFile() method instead
     * @param string $content
     * @return \App\Models\File
     */
    public function uploadBase64EncodedPng(string $content): File
    {
        return $this->uploadBase64EncodedFile($content);
    }

    /**
     * Get a file record which is a resized version of the current instance.
     *
     * @param int|null $maxDimension
     * @return \App\Models\File
     */
    public function resizedVersion(int $maxDimension = null): self
    {
        // If no resize then return current instance.
        if ($maxDimension === null) {
            return $this;
        }

        // Parameter validation.
        if ($maxDimension < 1 || $maxDimension > 1000) {
            throw new \InvalidArgumentException("Max dimension in not withing range [$maxDimension]");
        }

        $file = static::query()
            ->whereRaw('`meta`->>"$.type" = ?', [static::META_TYPE_RESIZED_IMAGE])
            ->whereRaw('`meta`->>"$.data.file_id" = ?', [$this->id])
            ->whereRaw('`meta`->>"$.data.max_dimension" = ?', [$maxDimension])
            ->first();

        // Create the resized version if it doesn't exist.
        if ($file === null) {
            /** @var \App\ImageTools\Resizer $resizer */
            $resizer = resolve(Resizer::class);

            /** @var \App\Models\File $file */
            $file = static::create([
                'filename' => $this->filename,
                'mime_type' => $this->mime_type,
                'meta' => [
                    'type' => static::META_TYPE_RESIZED_IMAGE,
                    'data' => [
                        'file_id' => $this->id,
                        'max_dimension' => $maxDimension,
                    ],
                ],
                'is_private' => $this->is_private,
            ]);

            $file->upload(
                $resizer->resize($this->getContent(), $maxDimension)
            );
        }

        return $file;
    }

    /**
     * Get a file record which is a resized version of the specified placeholder.
     *
     * @param int $maxDimension
     * @param string $placeholderFor
     * @throws \InvalidArgumentException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @return \App\Models\File
     */
    public static function resizedPlaceholder(int $maxDimension, string $placeholderFor): self
    {
        // Parameter validation.
        $validPlaceholdersFor = [
            static::META_PLACEHOLDER_FOR_ORGANISATION,
            static::META_PLACEHOLDER_FOR_SERVICE,
            static::META_PLACEHOLDER_FOR_COLLECTION_PERSONA,
            static::META_PLACEHOLDER_FOR_LOCATION,
            static::META_PLACEHOLDER_FOR_SERVICE_LOCATION,
        ];

        if (!in_array($placeholderFor, $validPlaceholdersFor)) {
            throw new \InvalidArgumentException("Invalid placeholder name [$placeholderFor]");
        }

        $file = static::query()
            ->whereRaw('`meta`->>"$.type" = ?', [static::META_TYPE_RESIZED_IMAGE])
            ->whereRaw('`meta`->>"$.data.placeholder_for" = ?', [$placeholderFor])
            ->whereRaw('`meta`->>"$.data.max_dimension" = ?', [$maxDimension])
            ->first();

        // Create the resized version if it doesn't exist.
        if ($file === null) {
            /** @var \App\ImageTools\Resizer $resizer */
            $resizer = resolve(Resizer::class);

            /** @var \App\Models\File $file */
            $file = static::create([
                'filename' => "$placeholderFor.png",
                'mime_type' => static::MIME_TYPE_PNG,
                'meta' => [
                    'type' => static::META_TYPE_RESIZED_IMAGE,
                    'data' => [
                        'placeholder_for' => $placeholderFor,
                        'max_dimension' => $maxDimension,
                    ],
                ],
                'is_private' => false,
            ]);

            $srcImageContent = Storage::disk('local')->get("/placeholders/$placeholderFor.png");
            $file->upload(
                $resizer->resize($srcImageContent, $maxDimension)
            );
        }

        return $file;
    }

    /**
     * @param string $mimeType
     * @param bool $withPeriod
     * @return string
     */
    public static function extensionFromMime(string $mimeType, bool $withPeriod = true): string
    {
        $map = [
            static::MIME_TYPE_PNG => '.png',
            static::MIME_TYPE_TXT => '.txt',
        ];

        if (!array_key_exists($mimeType, $map)) {
            throw new \InvalidArgumentException("The mime type [$mimeType] is not supported.");
        }

        return $withPeriod ? $map[$mimeType] : trim($map[$mimeType], '.');
    }

    /**
     * @return \App\Models\File
     */
    public function assigned(): self
    {
        $this->update(['meta' => null]);

        return $this;
    }
}
