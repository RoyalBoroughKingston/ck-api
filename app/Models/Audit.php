<?php

namespace App\Models;

use App\Models\Mutators\AuditMutators;
use App\Models\Relationships\AuditRelationships;
use App\Models\Scopes\AuditScopes;

class Audit extends Model
{
    use AuditMutators;
    use AuditRelationships;
    use AuditScopes;
}
