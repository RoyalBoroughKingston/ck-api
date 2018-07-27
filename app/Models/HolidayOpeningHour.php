<?php

namespace App\Models;

use App\Models\Mutators\HolidayOpeningHourMutators;
use App\Models\Relationships\HolidayOpeningHourRelationships;
use App\Models\Scopes\HolidayOpeningHourScopes;

class HolidayOpeningHour extends Model
{
    use HolidayOpeningHourMutators;
    use HolidayOpeningHourRelationships;
    use HolidayOpeningHourScopes;
}
