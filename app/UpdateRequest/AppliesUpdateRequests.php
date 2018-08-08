<?php

namespace App\UpdateRequest;

use App\Models\UpdateRequest;

interface AppliesUpdateRequests
{
    /**
     * Check if the update request is valid.
     *
     * @param \App\Models\UpdateRequest $updateRequest
     * @return bool
     */
    public function validateUpdateRequest(UpdateRequest $updateRequest): bool;

    /**
     * Apply the update request.
     *
     * @param \App\Models\UpdateRequest $updateRequest
     * @return \App\Models\UpdateRequest
     */
    public function applyUpdateRequest(UpdateRequest $updateRequest): UpdateRequest;
}
