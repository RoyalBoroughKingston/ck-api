<?php

namespace App\Observers;

use App\Models\CollectionTaxonomy;

class CollectionTaxonomyObserver
{
    /**
     * Handle to the collection taxonomy "created" event.
     *
     * @param  \App\Models\CollectionTaxonomy  $collectionTaxonomy
     * @return void
     */
    public function created(CollectionTaxonomy $collectionTaxonomy)
    {
        $collectionTaxonomy->touchServices();
    }

    /**
     * Handle the collection taxonomy "deleted" event.
     *
     * @param  \App\Models\CollectionTaxonomy  $collectionTaxonomy
     * @return void
     */
    public function deleted(CollectionTaxonomy $collectionTaxonomy)
    {
        $collectionTaxonomy->touchServices();
    }
}
