<?php

namespace App\Models\Relationships;

use App\Models\Taxonomy;

trait TaxonomyRelationships
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Taxonomy::class, 'parent_id');
    }
}
