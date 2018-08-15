<?php

namespace App\Models;

use App\Geocode\Coordinate;
use App\Models\Mutators\CachedGeocodeResultMutators;
use App\Models\Relationships\CachedGeocodeResultRelationships;
use App\Models\Scopes\CachedGeocodeResultScopes;

class CachedGeocodeResult extends Model
{
    use CachedGeocodeResultMutators;
    use CachedGeocodeResultRelationships;
    use CachedGeocodeResultScopes;

    /**
     * @return bool
     */
    public function hasNoCoordinate(): bool
    {
        return $this->lat === null || $this->lon === null;
    }

    /**
     * @return \App\Geocode\Coordinate
     */
    public function toCoordinate(): Coordinate
    {
        return new Coordinate($this->lat, $this->lon);
    }
}
