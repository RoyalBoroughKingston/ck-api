<?php

namespace App\Models\Mutators;

use App\Support\Time;

trait HolidayOpeningHourMutators
{
    /**
     * @param string $opensAt
     * @return \App\Support\Time
     */
    public function getOpensAtAttribute(string $opensAt): Time
    {
        return Time::create($opensAt);
    }

    /**
     * @param \App\Support\Time $opensAt
     */
    public function setOpensAtAttribute(Time $opensAt)
    {
        $this->attributes['opens_at'] = $opensAt->toString();
    }

    /**
     * @param string $closesAt
     * @return \App\Support\Time
     */
    public function getClosesAtAttribute(string $closesAt): Time
    {
        return Time::create($closesAt);
    }

    /**
     * @param \App\Support\Time $closesAt
     */
    public function setClosesAtAttribute(Time $closesAt)
    {
        $this->attributes['closes_at'] = $closesAt->toString();
    }
}
