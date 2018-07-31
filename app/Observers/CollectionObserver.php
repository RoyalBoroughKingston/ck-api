<?php

namespace App\Observers;

use App\Models\Collection;

class CollectionObserver
{
    /**
     * Handle the collection "saved" event.
     *
     * @param  \App\Models\Collection  $collection
     * @return void
     */
    public function saved(Collection $collection)
    {
        Collection::query()
            ->where('type', $collection->type)
            ->where('id', '!=', $collection->id)
            ->where('order', '>=', $collection->order)
            ->increment('order');
    }
}
