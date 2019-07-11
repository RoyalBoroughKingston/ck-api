<?php

namespace App\Http\Resources;

use Carbon\CarbonImmutable;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'notifiable_type' => $this->notifiable_type,
            'notifiable_id' => $this->notifiable_id,
            'channel' => $this->channel,
            'recipient' => $this->recipient,
            'message' => $this->message,
            'sent_at' => optional($this->sent_at)->format(CarbonImmutable::ISO8601),
            'failed_at' => optional($this->failed_at)->format(CarbonImmutable::ISO8601),
            'created_at' => $this->created_at->format(CarbonImmutable::ISO8601),
            'updated_at' => $this->updated_at->format(CarbonImmutable::ISO8601),
        ];
    }
}
