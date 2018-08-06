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
}
