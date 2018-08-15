<?php

namespace App\Geocode;

use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;

class Coordinate implements Arrayable
{
    /**
     * @var float
     */
    protected $lat;

    /**
     * @var float
     */
    protected $lon;

    /**
     * Coordinate constructor.
     *
     * @param float $lat
     * @param float $lon
     */
    public function __construct(float $lat, float $lon)
    {
        if ($lat < -90 || $lat > 90) {
            throw new InvalidArgumentException("Illegal latitude value [$lat]");
        }

        if ($lon < -180 || $lon > 180) {
            throw new InvalidArgumentException("Illegal longitude value [$lon]");
        }

        $this->lat = $lat;
        $this->lon = $lon;
    }

    /**
     * @return float
     */
    public function lat(): float
    {
        return $this->lat;
    }

    /**
     * @return float
     */
    public function lon(): float
    {
        return $this->lon;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [$this->lat, $this->lon];
    }
}
