<?php

namespace App\Observers;

use App\Models\Taxonomy;

class TaxonomyObserver
{
    /**
     * Handle the collection "created" event.
     *
     * @param  \App\Models\Taxonomy $taxonomy
     * @return void
     * @throws \Exception
     */
    public function created(Taxonomy $taxonomy)
    {
        // Updates the order for all other taxonomies with the same parent.
        Taxonomy::query()
            ->where('parent_id', $taxonomy->parent_id)
            ->where('id', '!=', $taxonomy->id)
            ->where('order', '>=', $taxonomy->order)
            ->increment('order');
    }

    /**
     * Handle the collection "updated" event.
     * TODO: Handle moving from one parent to another.
     *
     * @param  \App\Models\Taxonomy $taxonomy
     * @return void
     * @throws \Exception
     */
    public function updated(Taxonomy $taxonomy)
    {
        // Updates the order for all other taxonomies with the same parent.
        // Get all the ID's for the taxonomy siblings.
        $taxonomyOrders = Taxonomy::query()
            ->where('parent_id', $taxonomy->parent_id)
            ->pluck('order');

        // Set the original order to null.
        $originalOrder = null;

        // Get the total number of taxonomies.
        $taxonomyCount = $taxonomyOrders->count();

        // Loop through the number of taxonomies until the missing ID is found, as this must be the original order.
        foreach (range(1, $taxonomyCount) as $order) {
            if (!$taxonomyOrders->contains($order)) {
                $originalOrder = $order;
                break;
            }
        }

        // If the order number was not updated.
        if ($originalOrder === null) {
            return;
        }

        if ($originalOrder < $taxonomy->order) {
            // If the order has increased then decrement the other order behind.
            Taxonomy::query()
                ->where('parent_id', $taxonomy->parent_id)
                ->where('id', '!=', $taxonomy->id)
                ->where('order', '<=', $taxonomy->order)
                ->where('order', '>', $originalOrder)
                ->decrement('order');
        } else {
            // If the order has decreased then increment the other order ahead.
            Taxonomy::query()
                ->where('parent_id', $taxonomy->parent_id)
                ->where('id', '!=', $taxonomy->id)
                ->where('order', '>=', $taxonomy->order)
                ->where('order', '<', $originalOrder)
                ->increment('order');
        }
    }

    /**
     * Handle the organisation "deleting" event.
     *
     * @param  \App\Models\Taxonomy $taxonomy
     * @return void
     */
    public function deleting(Taxonomy $taxonomy)
    {
        $taxonomy->collectionTaxonomies()->delete();
        $taxonomy->serviceTaxonomies()->delete();
    }

    /**
     * Handle the collection "deleted" event.
     *
     * @param  \App\Models\Taxonomy $taxonomy
     * @return void
     * @throws \Exception
     */
    public function deleted(Taxonomy $taxonomy)
    {
        // Updates the order for all other taxonomies with the same parent.
        Taxonomy::query()
            ->where('parent_id', $taxonomy->parent_id)
            ->where('order', '>', $taxonomy->order)
            ->decrement('order');
    }
}
