<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ReferralResource extends JsonResource
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
            'id' => $this->id,
            'service_id' => $this->service_id,
            'reference' => $this->reference,
            'status' => $this->status,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'other_contact' => $this->other_contact,
            'postcode_outward_code' => $this->postcode_outward_code,
            'comments' => $this->comments,
            'referral_consented_at' => optional($this->referral_consented_at)->format(Carbon::ISO8601),
            'feedback_consented_at' => optional($this->feedback_consented_at)->format(Carbon::ISO8601),
            'referee_name' => $this->referee_name,
            'referee_email' => $this->referee_email,
            'referee_phone' => $this->referee_phone,
            'referee_organisation' => $this->organisationTaxonomy->name ?? $this->organisation,
            'created_at' => $this->created_at->format(Carbon::ISO8601),
            'updated_at' => $this->updated_at->format(Carbon::ISO8601),
        ];
    }
}
