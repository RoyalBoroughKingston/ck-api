<?php

namespace App\Observers;

use App\Models\Collection;

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
        // Updates the order for all other collections of the same type.
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
        // Updates the order for all other collections of the same type.
        // Get all the ID's for the collection type.
        $collectionOrders = Collection::query()
            ->where('type', $collection->type)
            ->pluck('order');

        // Set the original order to null.
        $originalOrder = null;

        // Get the total number of collections.
        $collectionCount = $collectionOrders->count();

        // Loop through the number of collections until the missing ID is found, as this must be the original order.
        foreach (range(1, $collectionCount) as $order) {
            if (!$collectionOrders->contains($order)) {
                $originalOrder = $order;
                break;
            }
        }

        // If the order number was not updated.
        if ($originalOrder === null) {
            return;
        }

        if ($originalOrder < $collection->order) {
            // If the order has increased then decrement the other order behind.
            Collection::query()
                ->where('type', $collection->type)
                ->where('id', '!=', $collection->id)
                ->where('order', '<=', $collection->order)
                ->where('order', '>', $originalOrder)
                ->decrement('order');
        } else {
            // If the order has decreased then increment the other order ahead.
            Collection::query()
                ->where('type', $collection->type)
                ->where('id', '!=', $collection->id)
                ->where('order', '>=', $collection->order)
                ->where('order', '<', $originalOrder)
                ->increment('order');
        }
    }

    /**
     * Handle the organisation "deleting" event.
     *
     * @param  \App\Models\Collection $collection
     * @return void
     */
    public function deleting(Collection $collection)
    {
        $collection->collectionTaxonomies()->delete();
    }

    /**
     * Handle the collection "deleted" event.
     *
     * @param  \App\Models\Collection $collection
     * @return void
     * @throws \Exception
     */
    public function deleted(Collection $collection)
    {
        // Updates the order for all other collections of the same type.
        Collection::query()
            ->where('type', $collection->type)
            ->where('order', '>', $collection->order)
            ->decrement('order');
    }
}
