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
        $baseQuery = Taxonomy::topLevelCategories();
        $categories = QueryBuilder::for($baseQuery)
            ->with('children.children.children.children.children')
            ->get();

        event(EndpointHit::onRead($request, 'Viewed all taxonomy categories'));

        return TaxonomyCategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Taxonomy $category
     * @return \Illuminate\Http\Response
     */
    public function show(ShowRequest $request, Taxonomy $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Taxonomy $category
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Taxonomy $category)
    {
        //
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
