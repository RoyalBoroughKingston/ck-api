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

    /**
     * @return \App\Models\ReportType
     */
    public static function commissionersReport(): self
    {
        return static::where('name', 'Commissioners Report')->firstOrFail();
    }
}
