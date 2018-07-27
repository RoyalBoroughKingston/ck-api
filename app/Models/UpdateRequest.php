<?php

namespace App\Models;

use App\Models\Mutators\UpdateRequestMutators;
use App\Models\Relationships\UpdateRequestRelationships;
use App\Models\Scopes\UpdateRequestScopes;

class UpdateRequest extends Model
{
    use UpdateRequestMutators;
    use UpdateRequestRelationships;
    use UpdateRequestScopes;
}
