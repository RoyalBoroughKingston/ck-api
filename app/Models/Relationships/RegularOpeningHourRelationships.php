<?php

namespace App\Models\Relationships;

use App\Models\ServiceLocation;

trait RegularOpeningHourRelationships
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function serviceLocation()
    {
        return $this->belongsTo(ServiceLocation::class);
    }
}
