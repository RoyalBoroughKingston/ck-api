<?php

namespace App\Models\Relationships;

use App\Models\HolidayOpeningHour;
use App\Models\Location;
use App\Models\RegularOpeningHour;
use App\Models\Service;

trait ServiceLocationRelationships
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function regularOpeningHours()
    {
        return $this->hasMany(RegularOpeningHour::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function holidayOpeningHours()
    {
        return $this->hasMany(HolidayOpeningHour::class);
    }
}
