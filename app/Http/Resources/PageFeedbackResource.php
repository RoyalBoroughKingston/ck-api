<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class PageFeedbackResource extends JsonResource
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
            'url' => $this->url,
            'feedback' => $this->feedback,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'consented_at' => $this->consented_at->format(Carbon::ISO8601),
            'created_at' => $this->created_at->format(Carbon::ISO8601),
            'updated_at' => $this->updated_at->format(Carbon::ISO8601),
        ];
    }
}
