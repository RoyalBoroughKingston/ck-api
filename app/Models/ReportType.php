<?php

namespace App\Models;

use App\Models\Mutators\ReportTypeMutators;
use App\Models\Relationships\ReportTypeRelationships;
use App\Models\Scopes\ReportTypeScopes;

class ReportType extends Model
{
    use ReportTypeMutators;
    use ReportTypeRelationships;
    use ReportTypeScopes;
}
