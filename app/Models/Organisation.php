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
        // Differentiate between the update request types.
        $isForLogo = isset($updateRequest->data['logo_file_id']);

        if ($isForLogo) {
            $rules = ['logo_file_id' => ['required', 'exists:files,id']];
        } else {
            $rules = (new UpdateOrganisationRequest())->rules();
        }

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
        // Differentiate between the update request types.
        $isForLogo = isset($updateRequest->data['logo_file_id']);

        if ($isForLogo) {
            $this->update(['logo_file_id' => $updateRequest->data['logo_file_id']]);
        } else {
            $this->update([
                'slug' => $updateRequest->data['slug'],
                'name' => $updateRequest->data['name'],
                'description' => $updateRequest->data['description'],
                'url' => $updateRequest->data['url'],
                'email' => $updateRequest->data['email'],
                'phone' => $updateRequest->data['phone'],
            ]);
        }

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
}
