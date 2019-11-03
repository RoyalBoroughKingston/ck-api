<?php

namespace App\Models;

use App\Http\Requests\Organisation\UpdateRequest as UpdateOrganisationRequest;
use App\Models\Mutators\OrganisationMutators;
use App\Models\Relationships\OrganisationRelationships;
use App\Models\Scopes\OrganisationScopes;
use App\Rules\FileIsMimeType;
use App\UpdateRequest\AppliesUpdateRequests;
use App\UpdateRequest\UpdateRequests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

class Organisation extends Model implements AppliesUpdateRequests
{
    use OrganisationMutators;
    use OrganisationRelationships;
    use OrganisationScopes;
    use UpdateRequests;

    /**
     * Check if the update request is valid.
     *
     * @param \App\Models\UpdateRequest $updateRequest
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validateUpdateRequest(UpdateRequest $updateRequest): Validator
    {
        $rules = (new UpdateOrganisationRequest())
            ->merge(['organisation' => $this])
            ->rules();

        // Remove the pending assignment rule since the file is now uploaded.
        $rules['logo_file_id'] = [
            'nullable',
            'exists:files,id',
            new FileIsMimeType(File::MIME_TYPE_PNG),
        ];

        return ValidatorFacade::make($updateRequest->data, $rules);
    }

    /**
     * Apply the update request.
     *
     * @param \App\Models\UpdateRequest $updateRequest
     * @return \App\Models\UpdateRequest
     */
    public function applyUpdateRequest(UpdateRequest $updateRequest): UpdateRequest
    {
        $data = $updateRequest->data;

        $this->update([
            'slug' => $data['slug'] ?? $this->slug,
            'name' => $data['name'] ?? $this->name,
            'description' => sanitize_markdown($data['description'] ?? $this->description),
            'url' => $data['url'] ?? $this->url,
            'email' => $data['email'] ?? $this->email,
            'phone' => $data['phone'] ?? $this->phone,
            'logo_file_id' => array_key_exists('logo_file_id', $data)
                ? $data['logo_file_id']
                : $this->logo_file_id,
        ]);

        return $updateRequest;
    }

    /**
     * Custom logic for returning the data. Useful when wanting to transform
     * or modify the data before returning it, e.g. removing passwords.
     *
     * @param array $data
     * @return array
     */
    public function getData(array $data): array
    {
        return $data;
    }

    /**
     * @return \App\Models\Organisation
     */
    public function touchServices(): Organisation
    {
        $this->services()->get()->each->save();

        return $this;
    }

    /**
     * @return bool
     */
    public function hasLogo(): bool
    {
        return $this->logo_file_id !== null;
    }

    /**
     * @param int|null $maxDimension
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException|\InvalidArgumentException
     * @return \App\Models\File|\Illuminate\Http\Response|\Illuminate\Contracts\Support\Responsable
     */
    public static function placeholderLogo(int $maxDimension = null)
    {
        if ($maxDimension !== null) {
            return File::resizedPlaceholder($maxDimension, File::META_PLACEHOLDER_FOR_ORGANISATION);
        }

        return response()->make(
            Storage::disk('local')->get('/placeholders/organisation.png'),
            Response::HTTP_OK,
            ['Content-Type' => File::MIME_TYPE_PNG]
        );
    }
}
