<?php

namespace App\Models;

use App\Models\Mutators\ServiceGalleryItemMutators;
use App\Models\Relationships\ServiceGalleryItemRelationships;
use App\Models\Scopes\ServiceGalleryItemScopes;

class ServiceGalleryItem extends Model
{
    use ServiceGalleryItemMutators;
    use ServiceGalleryItemRelationships;
    use ServiceGalleryItemScopes;

    /**
     * @return string
     */
    public function url(): string
    {
        return route('core.v1.services.gallery-items', [
            'service' => $this->service_id,
            'file' => $this->file_id,
        ]);
    }
}
