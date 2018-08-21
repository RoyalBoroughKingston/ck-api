<?php

namespace App\Models\Scopes;

use App\Models\Location;
use App\Support\Coordinate;
use Illuminate\Database\Eloquent\Builder;

trait LocationScopes
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \App\Support\Coordinate $location
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByDistance(Builder $query, Coordinate $location): Builder
    {
        $latColumn = table(Location::class, 'lat');
        $lonColumn = table(Location::class, 'lon');

        $sql = "(acos(
            cos(radians({$location->lat()})) * 
            cos(radians($latColumn)) * 
            cos(radians($lonColumn) - radians({$location->lon()})) + 
            sin(radians({$location->lat()})) * 
            sin(radians($latColumn)) 
        ))";

        return $query->orderByRaw($sql);
    }
}
