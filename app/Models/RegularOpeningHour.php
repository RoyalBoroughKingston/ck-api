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
}
