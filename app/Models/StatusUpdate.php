<?php

namespace App\Models;

use App\Models\Mutators\StatusUpdateMutators;
use App\Models\Relationships\StatusUpdateRelationships;
use App\Models\Scopes\StatusUpdateScopes;

class StatusUpdate extends Model
{
    use StatusUpdateMutators;
    use StatusUpdateRelationships;
    use StatusUpdateScopes;
}
