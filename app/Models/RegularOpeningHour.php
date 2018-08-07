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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'starts_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const FREQUENCY_WEEKLY = 'weekly';
    const FREQUENCY_MONTHLY = 'monthly';
    const FREQUENCY_FORTNIGHTLY = 'fortnightly';
    const FREQUENCY_NTH_OCCURRENCE_OF_MONTH = 'nth_occurrence_of_month';

    const WEEKDAY_MONDAY = 1;
    const WEEKDAY_TUESDAY = 2;
    const WEEKDAY_WEDNESDAY = 3;
    const WEEKDAY_THURSDAY = 4;
    const WEEKDAY_FRIDAY = 5;
    const WEEKDAY_SATURDAY = 6;
    const WEEKDAY_SUNDAY = 7;
}
