<?php

namespace App\Observers;

use App\Models\Taxonomy;

class TaxonomyObserver
{
    /**
     * Handle the taxonomy "created" event.
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
     * Handle the taxonomy "updating" event.
     *
     * @param  \App\Models\Taxonomy $taxonomy
     * @return void
     * @throws \Exception
     */
    public function updating(Taxonomy $taxonomy)
    {
        // Get the original parent ID.
        $originalParentId = $taxonomy->getOriginal('parent_id');

        if ($originalParentId === $taxonomy->parent_id) {
            $this->updateOrderForSameParent($taxonomy);
        } else {
            $this->updateOrderForDifferentParent($taxonomy);
        }
    }

    /**
     * @param \App\Models\Taxonomy $taxonomy
     */
    protected function updateOrderForSameParent(Taxonomy $taxonomy)
    {
        // Get the original order.
        $originalOrder = $taxonomy->getOriginal('order');

        // If the order number was not updated.
        if ($originalOrder === $taxonomy->order) {
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
     * @param \App\Models\Taxonomy $taxonomy
     */
    protected function updateOrderForDifferentParent(Taxonomy $taxonomy)
    {
        // Get all siblings from old parent and decrement order for taxonomies with a higher order.
        Taxonomy::query()
            ->where('parent_id', $taxonomy->getOriginal('parent_id'))
            ->where('order', '>', $taxonomy->getOriginal('order'))
            ->decrement('order');

        // Increment taxonomies in the new parent to make space from the order specified.
        Taxonomy::query()
            ->where('parent_id', $taxonomy->parent_id)
            ->where('id', '!=', $taxonomy->id)
            ->where('order', '>=', $taxonomy->order)
            ->increment('order');
    }

    /**
     * Handle the taxonomy "updating" event.
     *
     * @param  \App\Models\Taxonomy $taxonomy
     * @return void
     */
    public function updated(Taxonomy $taxonomy)
    {
        $taxonomy->touchServices();
    }

    /**
     * Handle the organisation "deleting" event.
     *
     * @param  \App\Models\Taxonomy $taxonomy
     * @return void
     */
    public function deleting(Taxonomy $taxonomy)
    {
        $taxonomy->collectionTaxonomies->each->delete();
        $taxonomy->serviceTaxonomies->each->delete();

        // Set the parent ID to null for all children before deleting them.
        $children = $taxonomy->children;
        $taxonomy->children()->update(['parent_id' => null]);
        $children->each->delete();
    }

    /**
     * Handle the taxonomy "deleted" event.
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
