<?php

namespace App\Models;

use App\Models\Mutators\UsefulInfoMutators;
use App\Models\Relationships\UsefulInfoRelationships;
use App\Models\Scopes\UsefulInfoScopes;

class UsefulInfo extends Model
{
    use UsefulInfoMutators;
    use UsefulInfoRelationships;
    use UsefulInfoScopes;
}
