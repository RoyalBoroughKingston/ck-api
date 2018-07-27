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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_closed' => 'boolean',
        'starts_at' => 'date',
        'ends_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
