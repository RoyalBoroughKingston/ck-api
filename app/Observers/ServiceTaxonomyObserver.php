<?php

namespace App\Observers;

use App\Models\ServiceTaxonomy;

class ServiceTaxonomyObserver
{
    /**
     * Handle to the service taxonomy "created" event.
     *
     * @param \App\Models\ServiceTaxonomy $serviceTaxonomy
     */
    public function created(ServiceTaxonomy $serviceTaxonomy)
    {
        $serviceTaxonomy->touchService();
    }

    /**
     * Handle the service taxonomy "deleted" event.
     *
     * @param \App\Models\ServiceTaxonomy $serviceTaxonomy
     */
    public function deleted(ServiceTaxonomy $serviceTaxonomy)
    {
        $serviceTaxonomy->touchService();
    }
}
