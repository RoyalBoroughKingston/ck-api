<?php

namespace App\Models\Relationships;

use App\Models\Service;
use App\Models\StatusUpdate;
use App\Models\Taxonomy;

trait ReferralRelationships
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statusUpdates()
    {
        return $this->hasMany(StatusUpdate::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organisationTaxonomy()
    {
        return $this->belongsTo(Taxonomy::class, 'organisation_taxonomy_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function latestCompletedStatusUpdate()
    {
        return $this->hasMany(StatusUpdate::class)
            ->orderByDesc(table(StatusUpdate::class, 'created_at'))
            ->where(table(StatusUpdate::class, 'to'), '=', StatusUpdate::TO_COMPLETED)
            ->take(1);
    }
}
