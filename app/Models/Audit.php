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

    const ACTION_CREATE = 'create';
    const ACTION_READ = 'read';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';

    const AUTO_DELETE_MONTHS = 24;
}
