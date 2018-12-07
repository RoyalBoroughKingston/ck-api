<?php

namespace App\Models;

use App\Models\Mutators\PageFeedbackMutators;
use App\Models\Relationships\PageFeedbackRelationships;
use App\Models\Scopes\PageFeedbackScopes;

class PageFeedback extends Model
{
    use PageFeedbackMutators;
    use PageFeedbackRelationships;
    use PageFeedbackScopes;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'consented_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
