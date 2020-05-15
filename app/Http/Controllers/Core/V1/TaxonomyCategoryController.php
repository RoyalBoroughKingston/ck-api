<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaxonomyCategory\DestroyRequest;
use App\Http\Requests\TaxonomyCategory\IndexRequest;
use App\Http\Requests\TaxonomyCategory\ShowRequest;
use App\Http\Requests\TaxonomyCategory\StoreRequest;
use App\Http\Requests\TaxonomyCategory\UpdateRequest;
use App\Http\Resources\TaxonomyCategoryResource;
use App\Http\Responses\ResourceDeleted;
use App\Models\Taxonomy;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

class TaxonomyCategoryController extends Controller
{
    /**
     * TaxonomyCategoryController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\TaxonomyCategory\IndexRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $baseQuery = Taxonomy::query()
            ->topLevelCategories()
            ->with('children.children.children.children.children.children')
            ->orderBy('order');

        $categories = QueryBuilder::for($baseQuery)
            ->get();

        event(EndpointHit::onRead($request, 'Viewed all taxonomy categories'));

        return TaxonomyCategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\TaxonomyCategory\StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $parent = $request->filled('parent_id')
                ? Taxonomy::query()->firstOrFail($request->parent_id)
                : Taxonomy::category();

            $category = Taxonomy::create([
                'parent_id' => $parent->id,
                'name' => $request->name,
                'order' => $request->order,
                'depth' => 1 + $parent->getDepth(),
            ]);

            event(EndpointHit::onCreate($request, "Created taxonomy category [{$category->id}]", $category));

            return new TaxonomyCategoryResource($category);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\TaxonomyCategory\ShowRequest $request
     * @param \App\Models\Taxonomy $taxonomy
     * @return \App\Http\Resources\TaxonomyCategoryResource
     */
    public function show(ShowRequest $request, Taxonomy $taxonomy)
    {
        $baseQuery = Taxonomy::query()
            ->where('id', $taxonomy->id);

        $taxonomy = QueryBuilder::for($baseQuery)
            ->firstOrFail();

        event(EndpointHit::onRead($request, "Viewed taxonomy category [{$taxonomy->id}]", $taxonomy));

        return new TaxonomyCategoryResource($taxonomy->load('children.children.children.children.children.children'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\TaxonomyCategory\UpdateRequest $request
     * @param \App\Models\Taxonomy $taxonomy
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Taxonomy $taxonomy)
    {
        return DB::transaction(function () use ($request, $taxonomy) {
            $parent = $request->filled('parent_id')
                ? Taxonomy::query()->firstOrFail($request->parent_id)
                : Taxonomy::category();

            $taxonomy->update([
                'parent_id' => $parent->id,
                'name' => $request->name,
                'order' => $request->order,
                'depth' => 1 + $parent->getDepth(),
            ]);

            event(EndpointHit::onUpdate($request, "Updated taxonomy category [{$taxonomy->id}]", $taxonomy));

            return new TaxonomyCategoryResource($taxonomy);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\TaxonomyCategory\DestroyRequest $request
     * @param \App\Models\Taxonomy $taxonomy
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, Taxonomy $taxonomy)
    {
        return DB::transaction(function () use ($request, $taxonomy) {
            event(EndpointHit::onDelete($request, "Deleted taxonomy category [{$taxonomy->id}]", $taxonomy));

            $taxonomy->delete();

            return new ResourceDeleted('taxonomy category');
        });
    }
}
