<?php

namespace App\Models;

use App\Models\Mutators\CachedGeocodeResultMutators;
use App\Models\Relationships\CachedGeocodeResultRelationships;
use App\Models\Scopes\CachedGeocodeResultScopes;
use App\Support\Coordinate;

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
     * @return \App\Support\Coordinate
     */
    public function toCoordinate(): Coordinate
    {
        return new Coordinate($this->lat, $this->lon);
    }
}
