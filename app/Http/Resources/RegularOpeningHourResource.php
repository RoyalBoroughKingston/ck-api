<?php

namespace App\Http\Resources;

use App\Models\RegularOpeningHour;
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
            'weekday' => $this->when(
                in_array(
                    $this->frequency,
                    [
                        RegularOpeningHour::FREQUENCY_WEEKLY,
                        RegularOpeningHour::FREQUENCY_NTH_OCCURRENCE_OF_MONTH,
                    ]
                ),
                $this->weekday
            ),
            'day_of_month' => $this->when($this->frequency === RegularOpeningHour::FREQUENCY_MONTHLY, $this->day_of_month),
            'occurrence_of_month' => $this->when($this->frequency === RegularOpeningHour::FREQUENCY_NTH_OCCURRENCE_OF_MONTH, $this->occurrence_of_month),
            'starts_at' => $this->when($this->frequency === RegularOpeningHour::FREQUENCY_FORTNIGHTLY, optional($this->starts_at)->toDateString()),
            'opens_at' => $this->opens_at->toString(),
            'closes_at' => $this->closes_at->toString(),
        ];
    }
}
