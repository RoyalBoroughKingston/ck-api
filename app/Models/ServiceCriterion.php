<?php

namespace App\Models;

use App\Models\Mutators\ServiceCriterionMutators;
use App\Models\Relationships\ServiceCriterionRelationships;
use App\Models\Scopes\ServiceCriterionScopes;

class ServiceCriterion extends Model
{
    use ServiceCriterionMutators;
    use ServiceCriterionRelationships;
    use ServiceCriterionScopes;
}
