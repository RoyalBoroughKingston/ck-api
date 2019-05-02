<?php

namespace App\Models;

use App\Models\Mutators\OfferingMutators;
use App\Models\Relationships\OfferingRelationships;
use App\Models\Scopes\OfferingScopes;

class Offering extends Model
{
    use OfferingMutators;
    use OfferingRelationships;
    use OfferingScopes;
}
