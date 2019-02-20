<?php

namespace App\Models;

use App\Models\Mutators\CollectionMutators;
use App\Models\Relationships\CollectionRelationships;
use App\Models\Scopes\CollectionScopes;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class Collection extends Model
{
    use CollectionMutators;
    use CollectionRelationships;
    use CollectionScopes;

    const TYPE_CATEGORY = 'category';
    const TYPE_PERSONA = 'persona';

    /**
     * @return \App\Models\Collection
     */
    public function touchServices(): Collection
    {
        static::services($this)->get()->each->save();

        return $this;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $taxonomies
     * @return \App\Models\Collection
     */
    public function syncCollectionTaxonomies(EloquentCollection $taxonomies): Collection
    {
        // Delete all existing collection taxonomies.
        $this->collectionTaxonomies()->delete();

        // Create a collection taxonomy record for each taxonomy.
        foreach ($taxonomies as $taxonomy) {
            $this->collectionTaxonomies()->updateOrCreate(['taxonomy_id' => $taxonomy->id]);
        }

        return $this;
    }

    /**
     * @param int|null $maxDimension
     * @return \App\Models\File|\Illuminate\Http\Response|\Illuminate\Contracts\Support\Responsable
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException|\InvalidArgumentException
     */
    public static function personaPlaceholderLogo(int $maxDimension = null)
    {
        if ($maxDimension !== null) {
            return File::resizedPlaceholder($maxDimension, File::META_PLACEHOLDER_FOR_COLLECTION_PERSONA);
        }

        return response()->make(
            Storage::disk('local')->get('/placeholders/collection_persona.png'),
            Response::HTTP_OK,
            ['Content-Type' => File::MIME_TYPE_PNG]
        );
    }
}
