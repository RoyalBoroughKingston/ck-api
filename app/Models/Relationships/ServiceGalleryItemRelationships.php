<?php

namespace App\Models\Relationships;

use App\Models\File;

trait ServiceGalleryItemRelationships
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
