<?php

namespace App\Models\Relationships;

use App\Models\ServiceLocation;

trait LocationRelationships
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function serviceLocations()
    {
        return $this->hasMany(ServiceLocation::class);
    }
}
