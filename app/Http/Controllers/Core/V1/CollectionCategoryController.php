<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\CollectionCategory\CollectionCategoriesListed;
use App\Http\Requests\CollectionCategory\IndexRequest;
use App\Http\Requests\CollectionCategory\StoreRequest;
use App\Http\Resources\CollectionCategoryResource;
use App\Models\Collection;
use App\Models\CollectionTaxonomy;
use Illuminate\Http\Request;
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
        event(new CollectionCategoriesListed($request));

        $categories = QueryBuilder::for(Collection::categories())
            ->with('taxonomies')
            ->paginate();

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
            $collectionCategory = Collection::create([
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
                    'collection_id' => $collectionCategory->id,
                    'taxonomy_id' => $categoryTaxonomyId,
                ]);
            }

            // Reload the newly created pivot records.
            $collectionCategory->load('taxonomies');

            return new CollectionCategoryResource($collectionCategory);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Collection  $collection
     * @return \Illuminate\Http\Response
     */
    public function show(Collection $collection)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Collection  $collection
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Collection $collection)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Collection  $collection
     * @return \Illuminate\Http\Response
     */
    public function destroy(Collection $collection)
    {
        //
    }
}
