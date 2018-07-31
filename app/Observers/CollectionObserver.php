<?php

namespace App\Observers;

use App\Models\Collection;
use Exception;

class CollectionObserver
{
    /**
     * Handle the collection "created" event.
     *
     * @param  \App\Models\Collection $collection
     * @return void
     * @throws \Exception
     */
    public function created(Collection $collection)
    {
        /*
         * Updates the order for all other collections of the same type.
         */

        Collection::query()
            ->where('type', $collection->type)
            ->where('id', '!=', $collection->id)
            ->where('order', '>=', $collection->order)
            ->increment('order');
    }

    /**
     * Handle the collection "updated" event.
     *
     * @param  \App\Models\Collection $collection
     * @return void
     * @throws \Exception
     */
    public function updated(Collection $collection)
    {
        /*
         * Updates the order for all other collections of the same type.
         */

        // Get all the ID's for the collection type.
        $collectionOrders = Collection::query()
            ->where('type', $collection->type)
            ->pluck('order');

        // Set the original order to null.
        $originalOrder = null;

        // Loop through the number of collections until the missing ID is found, as this must be the original order.
        foreach (range(1, $collectionOrders->count()) as $order) {
            if (!$collectionOrders->contains($order)) {
                $originalOrder = $order;
                break;
            }
        }

        // The original order should always have been found.
        if ($originalOrder === null) {
            throw new Exception('Could not find original order number');
        }

        if ($originalOrder < $collection->order) {
            // If the order has increased then decrement the other order behind.
            Collection::query()
                ->where('type', $collection->type)
                ->where('id', '!=', $collection->id)
                ->where('order', '<=', $collection->order)
                ->decrement('order');
        } else {
            // If the order has decreased then increment the other order ahead.
            Collection::query()
                ->where('type', $collection->type)
                ->where('id', '!=', $collection->id)
                ->where('order', '>=', $collection->order)
                ->increment('order');
        }
    }
}
