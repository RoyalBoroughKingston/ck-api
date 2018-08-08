<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Requests\TaxonomyCategory\DestroyRequest;
use App\Http\Requests\TaxonomyCategory\IndexRequest;
use App\Http\Requests\TaxonomyCategory\ShowRequest;
use App\Http\Requests\TaxonomyCategory\StoreRequest;
use App\Http\Requests\TaxonomyCategory\UpdateRequest;
use App\Http\Resources\TaxonomyCategoryResource;
use App\Models\Taxonomy;
use App\Http\Controllers\Controller;
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
            ->orderBy('order')
            ->with('children.children.children.children.children');
        $categories = QueryBuilder::for($baseQuery)->get();

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
            $category = Taxonomy::create([
                'parent_id' => $request->parent_id ?? Taxonomy::category()->id,
                'name' => $request->name,
                'order' => $request->order,
            ]);

            event(EndpointHit::onCreate($request, "Created taxonomy category [{$category->id}]", $category));

            return new TaxonomyCategoryResource($category);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\TaxonomyCategory\ShowRequest $request
     * @param  \App\Models\Taxonomy $category
     * @return \App\Http\Resources\TaxonomyCategoryResource
     */
    public function show(ShowRequest $request, Taxonomy $category)
    {
        event(EndpointHit::onRead($request, "Viewed taxonomy category [{$category->id}]", $category));

        return new TaxonomyCategoryResource($category->load('children.children.children.children.children'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\TaxonomyCategory\UpdateRequest $request
     * @param  \App\Models\Taxonomy $category
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Taxonomy $category)
    {
        return DB::transaction(function () use ($request, $category) {
            $category->update([
                'parent_id' => $request->parent_id ?? Taxonomy::category()->id,
                'name' => $request->name,
                'order' => $request->order,
            ]);

            event(EndpointHit::onUpdate($request, "Updated taxonomy category [{$category->id}]", $category));

            return new TaxonomyCategoryResource($category);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Taxonomy $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, Taxonomy $category)
    {
        //
    }
}
