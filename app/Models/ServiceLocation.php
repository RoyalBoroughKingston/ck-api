<?php

namespace App\Models;

use App\Models\Mutators\ServiceLocationMutators;
use App\Models\Relationships\ServiceLocationRelationships;
use App\Models\Scopes\ServiceLocationScopes;

class ServiceLocation extends Model
{
    use ServiceLocationMutators;
    use ServiceLocationRelationships;
    use ServiceLocationScopes;
    use UpdateRequests;
}
