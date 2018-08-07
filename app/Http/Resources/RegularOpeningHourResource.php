<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RegularOpeningHourResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'frequency' => $this->frequency,
            'weekday' => $this->weekday,
            'occurrence_of_month' => $this->occurrence_of_month,
            'starts_at' => optional($this->starts_at)->toDateString(),
            'opens_at' => $this->opens_at->toString(),
            'closes_at' => $this->closes_at->toString(),
        ];
    }
}
