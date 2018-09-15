<?php

namespace App\Models;

use App\Models\Mutators\SearchHistoryMutators;
use App\Models\Relationships\SearchHistoryRelationships;
use App\Models\Scopes\SearchHistoryScopes;

class SearchHistory extends Model
{
    use SearchHistoryMutators;
    use SearchHistoryRelationships;
    use SearchHistoryScopes;
}
