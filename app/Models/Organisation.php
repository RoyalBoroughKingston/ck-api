<?php

namespace App\Models;

use App\Models\Mutators\OrganisationMutators;
use App\Models\Relationships\OrganisationRelationships;
use App\Models\Scopes\OrganisationScopes;

class Organisation extends Model
{
    use OrganisationMutators;
    use OrganisationRelationships;
    use OrganisationScopes;
    use UpdateRequests;
}
