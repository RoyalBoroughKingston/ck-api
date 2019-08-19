<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceCriterionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'age_group' => $this->age_group,
            'disability' => $this->disability,
            'employment' => $this->employment,
            'gender' => $this->gender,
            'housing' => $this->housing,
            'income' => $this->income,
            'language' => $this->language,
            'other' => $this->other,
        ];
    }
}
