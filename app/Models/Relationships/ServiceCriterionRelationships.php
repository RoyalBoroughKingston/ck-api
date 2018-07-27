<?php

namespace App\Models\Relationships;

use App\Models\Service;

trait ServiceCriterionRelationships
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
