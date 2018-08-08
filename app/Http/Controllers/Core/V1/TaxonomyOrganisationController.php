<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Requests\TaxonomyOrganisation\DestroyRequest;
use App\Http\Requests\TaxonomyOrganisation\IndexRequest;
use App\Http\Requests\TaxonomyOrganisation\ShowRequest;
use App\Http\Requests\TaxonomyOrganisation\StoreRequest;
use App\Http\Requests\TaxonomyOrganisation\UpdateRequest;
use App\Http\Resources\TaxonomyOrganisationResource;
use App\Models\Taxonomy;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;

class TaxonomyOrganisationController extends Controller
{
    /**
     * TaxonomyOrganisationController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\TaxonomyOrganisation\IndexRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $baseQuery = Taxonomy::organisations()->orderBy('order');
        $organisations = QueryBuilder::for($baseQuery)->get();

        event(EndpointHit::onRead($request, 'Viewed all taxonomy organisations'));

        return TaxonomyOrganisationResource::collection($organisations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Taxonomy  $organisation
     * @return \Illuminate\Http\Response
     */
    public function show(ShowRequest $request, Taxonomy $organisation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Taxonomy  $organisation
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Taxonomy $organisation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Taxonomy  $organisation
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, Taxonomy $organisation)
    {
        //
    }
}
