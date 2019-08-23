<?php

namespace App\Http\Resources;

use Carbon\CarbonImmutable;
use Illuminate\Http\Resources\Json\JsonResource;

class PageFeedbackResource extends JsonResource
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
            'id' => $this->id,
            'url' => $this->url,
            'feedback' => $this->feedback,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'consented_at' => optional($this->consented_at)->format(CarbonImmutable::ISO8601),
            'created_at' => optional($this->created_at)->format(CarbonImmutable::ISO8601),
            'updated_at' => optional($this->updated_at)->format(CarbonImmutable::ISO8601),
        ];
    }
}
