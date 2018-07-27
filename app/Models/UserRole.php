<?php

namespace App\Models;

use App\Models\Mutators\UserRoleMutators;
use App\Models\Relationships\UserRoleRelationships;
use App\Models\Scopes\UserRoleScopes;

class UserRole extends Model
{
    use UserRoleMutators;
    use UserRoleRelationships;
    use UserRoleScopes;
}
