<?php

namespace App\Models;

use App\Http\Requests\Organisation\UpdateRequest as UpdateOrganisationRequest;
use App\Models\Mutators\OrganisationMutators;
use App\Models\Relationships\OrganisationRelationships;
use App\Models\Scopes\OrganisationScopes;
use App\UpdateRequest\AppliesUpdateRequests;
use App\UpdateRequest\UpdateRequests;
use Illuminate\Contracts\Validation\Validator;
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
        $this->update([
            'slug' => $updateRequest->data['slug'] ?? $this->slug,
            'name' => $updateRequest->data['name'] ?? $this->name,
            'description' => $updateRequest->data['description'] ?? $this->description,
            'url' => $updateRequest->data['url'] ?? $this->url,
            'email' => $updateRequest->data['email'] ?? $this->email,
            'phone' => $updateRequest->data['phone'] ?? $this->phone,
            'logo_file_id' => $updateRequest->data['logo_file_id'] ?? $this->logo_file_id,
        ]);

        return $updateRequest;
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
}
