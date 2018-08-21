<?php

namespace App\Support;

use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;

class Coordinate implements Arrayable
{
    const EARTH_RADIUS = 6371000;

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
        return [
            'lat' => $this->lat,
            'lon' => $this->lon,
        ];
    }

    /**
     * @param \App\Support\Coordinate $from
     * @return float
     */
    public function distanceFrom(Coordinate $from): float
    {
        // convert from degrees to radians
        $latFrom = deg2rad($from->lat());
        $lonFrom = deg2rad($from->lon());
        $latTo = deg2rad($this->lat());
        $lonTo = deg2rad($this->lon());

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * static::EARTH_RADIUS;
    }
}
