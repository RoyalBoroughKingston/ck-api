<?php

namespace App\Models;

use App\Models\Mutators\ServiceMutators;
use App\Models\Relationships\ServiceRelationships;
use App\Models\Scopes\ServiceScopes;

class Service extends Model
{
    use ServiceMutators;
    use ServiceRelationships;
    use ServiceScopes;
}
