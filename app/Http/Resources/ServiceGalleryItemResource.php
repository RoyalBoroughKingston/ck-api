<?php

namespace App\Http\Resources;

use Carbon\CarbonImmutable;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceGalleryItemResource extends JsonResource
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
            'file_id' => $this->file_id,
            'url' => $this->url(),
            'created_at' => $this->created_at->format(CarbonImmutable::ISO8601),
            'updated_at' => $this->updated_at->format(CarbonImmutable::ISO8601),
        ];
    }
}
