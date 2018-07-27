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
}
