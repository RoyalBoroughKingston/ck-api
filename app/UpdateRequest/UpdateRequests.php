<?php

namespace App\UpdateRequest;

use App\Models\UpdateRequest;

trait UpdateRequests
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function updateRequests()
    {
        return $this->morphMany(UpdateRequest::class, 'updateable');
    }
}
