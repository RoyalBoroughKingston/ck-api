<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaxonomyOrganisation\DestroyRequest;
use App\Http\Requests\TaxonomyOrganisation\IndexRequest;
use App\Http\Requests\TaxonomyOrganisation\ShowRequest;
use App\Http\Requests\TaxonomyOrganisation\StoreRequest;
use App\Http\Requests\TaxonomyOrganisation\UpdateRequest;
use App\Http\Resources\TaxonomyOrganisationResource;
use App\Http\Responses\ResourceDeleted;
use App\Models\Taxonomy;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

class TaxonomyOrganisationController extends Controller
{
    /**
     * TaxonomyOrganisationController constructor.
     */
    public function __construct()
    {
        $this->middleware('throttle:60,1');
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
        $baseQuery = Taxonomy::query()
            ->organisations()
            ->orderBy('order');

        $organisations = QueryBuilder::for($baseQuery)
            ->get();

        event(EndpointHit::onRead($request, 'Viewed all taxonomy organisations'));

        return TaxonomyOrganisationResource::collection($organisations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\TaxonomyOrganisation\StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $organisation = Taxonomy::organisation()->children()->create([
                'name' => $request->name,
                'order' => $request->order,
            ]);

            event(EndpointHit::onCreate($request, "Created taxonomy organisation [{$organisation->id}]", $organisation));

            return new TaxonomyOrganisationResource($organisation);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\TaxonomyOrganisation\ShowRequest $request
     * @param \App\Models\Taxonomy $taxonomy
     * @return \App\Http\Resources\TaxonomyOrganisationResource
     */
    public function show(ShowRequest $request, Taxonomy $taxonomy)
    {
        $baseQuery = Taxonomy::query()
            ->where('id', $taxonomy->id);

        $taxonomy = QueryBuilder::for($baseQuery)
            ->firstOrFail();

        event(EndpointHit::onRead($request, "Viewed taxonomy organisation [{$taxonomy->id}]", $taxonomy));

        return new TaxonomyOrganisationResource($taxonomy);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\TaxonomyOrganisation\UpdateRequest $request
     * @param \App\Models\Taxonomy $taxonomy
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Taxonomy $taxonomy)
    {
        return DB::transaction(function () use ($request, $taxonomy) {
            $taxonomy->update([
                'name' => $request->name,
                'order' => $request->order,
            ]);

            event(EndpointHit::onUpdate($request, "Updated taxonomy organisation [{$taxonomy->id}]", $taxonomy));

            return new TaxonomyOrganisationResource($taxonomy);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\TaxonomyOrganisation\DestroyRequest $request
     * @param \App\Models\Taxonomy $taxonomy
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, Taxonomy $taxonomy)
    {
        return DB::transaction(function () use ($request, $taxonomy) {
            event(EndpointHit::onDelete($request, "Deleted taxonomy organisation [{$taxonomy->id}]", $taxonomy));

            $taxonomy->delete();

            return new ResourceDeleted('taxonomy organisation');
        });
    }
}
