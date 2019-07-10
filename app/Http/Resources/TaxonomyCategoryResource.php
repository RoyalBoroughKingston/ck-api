<?php

namespace App\Http\Resources;

use App\Models\Taxonomy;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class TaxonomyCategoryResource extends JsonResource
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
            'parent_id' => $this->parent_id !== Taxonomy::category()->id ? $this->parent_id : null,
            'name' => $this->name,
            'order' => $this->order,
            'children' => static::collection($this->whenLoaded('children')),
            'created_at' => $this->created_at->format(Carbon::ISO8601),
            'updated_at' => $this->updated_at->format(Carbon::ISO8601),
        ];
    }
}
