<?php

namespace App\Models;

use App\Models\Mutators\LocationMutators;
use App\Models\Relationships\LocationRelationships;
use App\Models\Scopes\LocationScopes;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use LocationMutators;
    use LocationRelationships;
    use LocationScopes;
}
