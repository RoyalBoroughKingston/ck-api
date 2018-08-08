<?php

namespace App\Models;

use App\Models\Mutators\OrganisationMutators;
use App\Models\Relationships\OrganisationRelationships;
use App\Models\Scopes\OrganisationScopes;
use App\UpdateRequest\AppliesUpdateRequests;
use App\UpdateRequest\UpdateRequests;
use Illuminate\Support\Facades\Validator;

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
     * @return bool
     */
    public function validateUpdateRequest(UpdateRequest $updateRequest): bool
    {
        $rules = [
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'description' => ['required', 'string', 'min:1', 'max:10000'],
            'url' => ['required', 'url', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'min:1', 'max:255'],
        ];

        return Validator::make($updateRequest->data, $rules)->fails() === false;
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
            'name' => $updateRequest->data['name'],
            'description' => $updateRequest->data['description'],
            'url' => $updateRequest->data['url'],
            'email' => $updateRequest->data['email'],
            'phone' => $updateRequest->data['phone'],
        ]);

        return $updateRequest;
    }
}
