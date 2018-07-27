<?php

namespace App\Models;

use App\Models\Mutators\CollectionTaxonomyMutators;
use App\Models\Relationships\CollectionTaxonomyRelationships;
use App\Models\Scopes\CollectionTaxonomyScopes;

class CollectionTaxonomy extends Model
{
    use CollectionTaxonomyMutators;
    use CollectionTaxonomyRelationships;
    use CollectionTaxonomyScopes;
}
