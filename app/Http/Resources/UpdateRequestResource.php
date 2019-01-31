<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class UpdateRequestResource extends JsonResource
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
            'user_id' => $this->user_id,
            'updateable_type' => $this->updateable_type,
            'updateable_id' => $this->updateable_id,
            'data' => $this->data,
            'created_at' => $this->created_at->format(Carbon::ISO8601),
            'updated_at' => $this->updated_at->format(Carbon::ISO8601),
            'approved_at' => optional($this->approved_at)->format(Carbon::ISO8601),
            'deleted_at' => optional($this->deleted_at)->format(Carbon::ISO8601),

            // Relationships.
            'user' => new UserResource($this->whenLoaded('user')),

            // Appends.
            'entry' => $this->entry,
        ];
    }
}
