<?php

namespace App\Models;

use App\Models\Mutators\RoleMutators;
use App\Models\Relationships\RoleRelationships;
use App\Models\Scopes\RoleScopes;

class Role extends Model
{
    use RoleMutators;
    use RoleRelationships;
    use RoleScopes;
}
