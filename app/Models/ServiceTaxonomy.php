<?php

namespace App\Models;

use App\Models\Mutators\ServiceTaxonomyMutators;
use App\Models\Relationships\ServiceTaxonomyRelationships;
use App\Models\Scopes\ServiceTaxonomyScopes;

class ServiceTaxonomy extends Model
{
    use ServiceTaxonomyMutators;
    use ServiceTaxonomyRelationships;
    use ServiceTaxonomyScopes;

    /**
     * @return \App\Models\ServiceTaxonomy
     */
    public function touchService(): ServiceTaxonomy
    {
        $this->service->save();

        return $this;
    }
}
