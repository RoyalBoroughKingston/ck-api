<?php

namespace App\Models;

use App\Contracts\Geocoder;
use App\Models\Mutators\LocationMutators;
use App\Models\Relationships\LocationRelationships;
use App\Models\Scopes\LocationScopes;

class Location extends Model
{
    use LocationMutators;
    use LocationRelationships;
    use LocationScopes;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'lat' => 'float',
        'lon' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * @return \App\Models\Location
     */
    protected function updateCoordinate(): self
    {
        /**
         * @var \App\Contracts\Geocoder $geocoder
         */
        $geocoder = resolve(Geocoder::class);
        $address = sprintf('%s, %s, %s, %s', $this->address_line_1, $this->city, $this->postcode, $this->country);
        $coordinate = $geocoder->geocode($address);

        $this->lat = $coordinate->lat();
        $this->lon = $coordinate->lon();

        return $this;
    }
}
