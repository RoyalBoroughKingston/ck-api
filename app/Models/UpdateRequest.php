<?php

namespace App\Models;

use App\Models\Mutators\UpdateRequestMutators;
use App\Models\Relationships\UpdateRequestRelationships;
use App\Models\Scopes\UpdateRequestScopes;
use Illuminate\Database\Eloquent\SoftDeletes;

class UpdateRequest extends Model
{
    use SoftDeletes;
    use UpdateRequestMutators;
    use UpdateRequestRelationships;
    use UpdateRequestScopes;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
