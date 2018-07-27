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
}
