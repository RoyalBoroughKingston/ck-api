<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class OrganisationResource extends JsonResource
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
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'url' => $this->url,
            'email' => $this->email,
            'phone' => $this->phone,
            'created_at' => $this->created_at->format(Carbon::ISO8601),
            'updated_at' => $this->updated_at->format(Carbon::ISO8601),
        ];
    }
}
