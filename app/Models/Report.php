<?php

namespace App\Models;

use App\Models\Mutators\ReportMutators;
use App\Models\Relationships\ReportRelationships;
use App\Models\Scopes\ReportScopes;

class Report extends Model
{
    use ReportMutators;
    use ReportRelationships;
    use ReportScopes;
}
