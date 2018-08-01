<?php

namespace App\Models;

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
