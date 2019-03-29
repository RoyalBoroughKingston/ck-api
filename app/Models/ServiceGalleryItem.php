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
        // TODO: Generate from route().
        return '';
    }
}
