<?php

namespace App\Models;

use App\Models\Mutators\RegularOpeningHourMutators;
use App\Models\Relationships\RegularOpeningHourRelationships;
use App\Models\Scopes\RegularOpeningHourScopes;

class RegularOpeningHour extends Model
{
    use RegularOpeningHourMutators;
    use RegularOpeningHourRelationships;
    use RegularOpeningHourScopes;
}
