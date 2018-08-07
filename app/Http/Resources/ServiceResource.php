<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ServiceResource extends JsonResource
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
            'organisation_id' => $this->organisation_id,
            'name' => $this->name,
            'status' => $this->status,
            'intro' => $this->intro,
            'description' => $this->description,
            'wait_time' => $this->wait_time,
            'is_free' => $this->is_free,
            'fees_text' => $this->fees_text,
            'fees_url' => $this->fees_url,
            'testimonial' => $this->testimonial,
            'video_embed' => $this->video_embed,
            'url' => $this->url,
            'contact_name' => $this->contact_name,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
            'show_referral_disclaimer' => $this->show_referral_disclaimer,
            'referral_method' => $this->referral_method,
            'referral_button_text' => $this->referral_button_text,
            'referral_email' => $this->referral_email,
            'referral_url' => $this->referral_url,
            'criteria' => new ServiceCriterionResource($this->serviceCriterion),
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'useful_infos' => UsefulInfoResource::collection($this->usefulInfos),
            'social_medias' => SocialMediaResource::collection($this->socialMedias),
            'category_taxonomies' => TaxonomyResource::collection($this->taxonomies),
            'created_at' => $this->created_at->format(Carbon::ISO8601),
            'updated_at' => $this->updated_at->format(Carbon::ISO8601),
        ];
    }
}
