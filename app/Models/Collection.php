<?php

namespace App\Models;

use App\Models\Mutators\CollectionMutators;
use App\Models\Relationships\CollectionRelationships;
use App\Models\Scopes\CollectionScopes;

class Collection extends Model
{
    use CollectionMutators;
    use CollectionRelationships;
    use CollectionScopes;

    const TYPE_CATEGORY = 'category';
    const TYPE_PERSONA = 'persona';

    /**
     * @return \App\Models\Collection
     */
    public function touchServices(): Collection
    {
        static::services($this)->toBase()->update(['services.updated_at' => now()]);

        return $this;
    }
}
