<?php

namespace App\Http\Resources;

use Carbon\CarbonImmutable;
use Illuminate\Http\Resources\Json\JsonResource;

class StatusUpdateResource extends JsonResource
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
            'user_id' => $this->user_id,
            'referral_id' => $this->referral_id,
            'from' => $this->from,
            'to' => $this->to,
            'comments' => $this->comments,
            'created_at' => $this->created_at->format(CarbonImmutable::ISO8601),
            'updated_at' => $this->updated_at->format(CarbonImmutable::ISO8601),

            // Relationships.
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
