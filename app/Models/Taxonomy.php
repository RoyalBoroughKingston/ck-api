<?php

namespace App\Models;

use App\Models\Mutators\TaxonomyMutators;
use App\Models\Relationships\TaxonomyRelationships;
use App\Models\Scopes\TaxonomyScopes;

class Taxonomy extends Model
{
    use TaxonomyMutators;
    use TaxonomyRelationships;
    use TaxonomyScopes;
}
