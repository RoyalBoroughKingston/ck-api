<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\CollectionCategory\CollectionCategoryCreated;
use App\Events\CollectionCategory\CollectionCategoriesListed;
use App\Events\CollectionCategory\CollectionCategoryDeleted;
use App\Events\CollectionCategory\CollectionCategoryRead;
use App\Events\CollectionCategory\CollectionCategoryUpdated;
use App\Http\Requests\CollectionCategory\DestroyRequest;
use App\Http\Requests\CollectionCategory\IndexRequest;
use App\Http\Requests\CollectionCategory\ShowRequest;
use App\Http\Requests\CollectionCategory\StoreRequest;
use App\Http\Requests\CollectionCategory\UpdateRequest;
use App\Http\Resources\CollectionCategoryResource;
use App\Http\Responses\ResourceDeleted;
use App\Models\Collection;
use App\Models\CollectionTaxonomy;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

class CollectionCategoryController extends Controller
{
    /**
     * CategoryController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\CollectionCategory\IndexRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $categories = QueryBuilder::for(Collection::categories())
            ->with('taxonomies')
            ->paginate();

        event(new CollectionCategoriesListed($request));

        return CollectionCategoryResource::collection($categories);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\CollectionCategory\StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        return DB::transaction(function () use ($request) {
            // Create the collection record.
            $category = Collection::create([
                'type' => Collection::TYPE_CATEGORY,
                'name' => $request->name,
                'meta' => [
                    'intro' => $request->intro,
                    'icon' => $request->icon,
                ],
                'order' => $request->order,
            ]);

            // Create all of the pivot records.
            foreach ($request->category_taxonomies as $categoryTaxonomyId) {
                CollectionTaxonomy::create([
                    'collection_id' => $category->id,
                    'taxonomy_id' => $categoryTaxonomyId,
                ]);
            }

            // Reload the newly created pivot records.
            $category->load('taxonomies');

            event(new CollectionCategoryCreated($request, $category));

            return new CollectionCategoryResource($category);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\CollectionCategory\ShowRequest $request
     * @param  \App\Models\Collection $category
     * @return \App\Http\Resources\CollectionCategoryResource
     */
    public function show(ShowRequest $request, Collection $category)
    {
        event(new CollectionCategoryRead($request, $category));

        return new CollectionCategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\CollectionCategory\UpdateRequest $request
     * @param  \App\Models\Collection $category
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Collection $category)
    {
        return DB::transaction(function () use ($request, $category) {
            // Update the collection record.
            $category->update([
                'name' => $request->name,
                'meta' => [
                    'intro' => $request->intro,
                    'icon' => $request->icon,
                ],
                'order' => $request->order,
            ]);

            // Update or create all of the pivot records.
            foreach ($request->category_taxonomies as $categoryTaxonomyId) {
                CollectionTaxonomy::updateOrCreate([
                    'collection_id' => $category->id,
                    'taxonomy_id' => $categoryTaxonomyId,
                ]);
            }

            // Delete any pivot records that exist but were not submitted.
            $category->collectionTaxonomies()
                ->whereNotIn('taxonomy_id', $request->category_taxonomies)
                ->delete();

            event(new CollectionCategoryUpdated($request, $category));

            return new CollectionCategoryResource($category);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\CollectionCategory\DestroyRequest $request
     * @param  \App\Models\Collection $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, Collection $category)
    {
        return DB::transaction(function () use ($request, $category) {
            event(new CollectionCategoryDeleted($request, $category));

            $category->delete();

            return new ResourceDeleted('collection category');
        });
    }
}
