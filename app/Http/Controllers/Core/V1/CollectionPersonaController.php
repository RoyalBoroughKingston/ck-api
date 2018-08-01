<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\CollectionPersona\CollectionPersonaCreated;
use App\Events\CollectionPersona\CollectionPersonasListed;
use App\Events\CollectionPersona\CollectionPersonaDeleted;
use App\Events\CollectionPersona\CollectionPersonaRead;
use App\Events\CollectionPersona\CollectionPersonaUpdated;
use App\Http\Requests\CollectionPersona\DestroyRequest;
use App\Http\Requests\CollectionPersona\IndexRequest;
use App\Http\Requests\CollectionPersona\ShowRequest;
use App\Http\Requests\CollectionPersona\StoreRequest;
use App\Http\Requests\CollectionPersona\UpdateRequest;
use App\Http\Resources\CollectionPersonaResource;
use App\Http\Responses\ResourceDeleted;
use App\Models\Collection;
use App\Models\CollectionTaxonomy;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

class CollectionPersonaController extends Controller
{
    /**
     * CollectionPersonaController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\CollectionPersona\IndexRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $personas = QueryBuilder::for(Collection::personas())
            ->with('taxonomies')
            ->paginate();

        event(new CollectionPersonasListed($request));

        return CollectionPersonaResource::collection($personas);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\CollectionPersona\StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        return DB::transaction(function () use ($request) {
            // Create the collection record.
            $persona = Collection::create([
                'type' => Collection::TYPE_PERSONA,
                'name' => $request->name,
                'meta' => [
                    'intro' => $request->intro,
                    'subtitle' => $request->subtitle,
                ],
                'order' => $request->order,
            ]);

            // Create all of the pivot records.
            foreach ($request->category_taxonomies as $categoryTaxonomyId) {
                CollectionTaxonomy::create([
                    'collection_id' => $persona->id,
                    'taxonomy_id' => $categoryTaxonomyId,
                ]);
            }

            // Reload the newly created pivot records.
            $persona->load('taxonomies');

            event(new CollectionPersonaCreated($request, $persona));

            return new CollectionPersonaResource($persona);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\CollectionPersona\ShowRequest $request
     * @param  \App\Models\Collection $persona
     * @return \App\Http\Resources\CollectionPersonaResource
     */
    public function show(ShowRequest $request, Collection $persona)
    {
        event(new CollectionPersonaRead($request, $persona));

        return new CollectionPersonaResource($persona);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\CollectionPersona\UpdateRequest $request
     * @param  \App\Models\Collection $persona
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Collection $persona)
    {
        return DB::transaction(function () use ($request, $persona) {
            // Update the collection record.
            $persona->update([
                'name' => $request->name,
                'meta' => [
                    'intro' => $request->intro,
                    'subtitle' => $request->subtitle,
                ],
                'order' => $request->order,
            ]);

            // Update or create all of the pivot records.
            foreach ($request->category_taxonomies as $categoryTaxonomyId) {
                CollectionTaxonomy::updateOrCreate([
                    'collection_id' => $persona->id,
                    'taxonomy_id' => $categoryTaxonomyId,
                ]);
            }

            // Delete any pivot records that exist but were not submitted.
            $persona->collectionTaxonomies()
                ->whereNotIn('taxonomy_id', $request->category_taxonomies)
                ->delete();

            event(new CollectionPersonaUpdated($request, $persona));

            return new CollectionPersonaResource($persona);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\CollectionPersona\DestroyRequest $request
     * @param  \App\Models\Collection $persona
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, Collection $persona)
    {
        return DB::transaction(function () use ($request, $persona) {
            event(new CollectionPersonaDeleted($request, $persona));

            $persona->delete();

            return new ResourceDeleted('collection persona');
        });
    }
}
