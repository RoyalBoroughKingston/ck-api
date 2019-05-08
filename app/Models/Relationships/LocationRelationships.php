<?php

namespace App\Models\Relationships;

use App\Models\File;
use App\Models\Service;
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, (new ServiceLocation())->getTable());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function imageFile()
    {
        return $this->belongsTo(File::class, 'image_file_id');
    }
}
