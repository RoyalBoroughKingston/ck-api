<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
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
use App\Models\Taxonomy;
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

        event(EndpointHit::onRead($request, 'Viewed all collection categories'));

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
            $taxonomies = Taxonomy::whereIn('id', $request->category_taxonomies)->get();
            $category->syncCollectionTaxonomies($taxonomies);

            // Reload the newly created pivot records.
            $category->load('taxonomies');

            event(EndpointHit::onCreate($request, "Created collection category [{$category->id}]", $category));

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
        event(EndpointHit::onRead($request, "Viewed collection category [{$category->id}]", $category));

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
            $taxonomies = Taxonomy::whereIn('id', $request->category_taxonomies)->get();
            $category->syncCollectionTaxonomies($taxonomies);

            event(EndpointHit::onUpdate($request, "Updated collection category [{$category->id}]", $category));

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
            event(EndpointHit::onDelete($request, "Deleted collection category [{$category->id}]", $category));

            $category->delete();

            return new ResourceDeleted('collection category');
        });
    }
}
