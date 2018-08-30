<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Requests\CollectionPersona\DestroyRequest;
use App\Http\Requests\CollectionPersona\IndexRequest;
use App\Http\Requests\CollectionPersona\ShowRequest;
use App\Http\Requests\CollectionPersona\StoreRequest;
use App\Http\Requests\CollectionPersona\UpdateRequest;
use App\Http\Resources\CollectionPersonaResource;
use App\Http\Responses\ResourceDeleted;
use App\Models\Collection;
use App\Http\Controllers\Controller;
use App\Models\Taxonomy;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class CollectionPersonaController extends Controller
{
    /**
     * CollectionPersonaController constructor.
     */
    public function __construct()
    {
        $this->middleware('throttle:60,1');
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
        $baseQuery = Collection::personas()
            ->orderBy('order');

        $personas = QueryBuilder::for($baseQuery)
            ->allowedFilters([
                Filter::exact('id'),
            ])
            ->with('taxonomies')
            ->paginate(per_page($request->per_page));

        event(EndpointHit::onRead($request, 'Viewed all collection personas'));

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
            $taxonomies = Taxonomy::whereIn('id', $request->category_taxonomies)->get();
            $persona->syncCollectionTaxonomies($taxonomies);

            // Reload the newly created pivot records.
            $persona->load('taxonomies');

            event(EndpointHit::onCreate($request, "Created collection persona [{$persona->id}]", $persona));

            return new CollectionPersonaResource($persona);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\CollectionPersona\ShowRequest $request
     * @param  \App\Models\Collection $collection
     * @return \App\Http\Resources\CollectionPersonaResource
     */
    public function show(ShowRequest $request, Collection $collection)
    {
        $baseQuery = Collection::query()
            ->where('id', $collection->id);

        $collection = QueryBuilder::for($baseQuery)
            ->firstOrFail();

        event(EndpointHit::onRead($request, "Viewed collection persona [{$collection->id}]", $collection));

        return new CollectionPersonaResource($collection);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\CollectionPersona\UpdateRequest $request
     * @param  \App\Models\Collection $collection
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Collection $collection)
    {
        return DB::transaction(function () use ($request, $collection) {
            // Update the collection record.
            $collection->update([
                'name' => $request->name,
                'meta' => [
                    'intro' => $request->intro,
                    'subtitle' => $request->subtitle,
                ],
                'order' => $request->order,
            ]);

            // Update or create all of the pivot records.
            $taxonomies = Taxonomy::whereIn('id', $request->category_taxonomies)->get();
            $collection->syncCollectionTaxonomies($taxonomies);

            event(EndpointHit::onUpdate($request, "Updated collection persona [{$collection->id}]", $collection));

            return new CollectionPersonaResource($collection);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\CollectionPersona\DestroyRequest $request
     * @param  \App\Models\Collection $collection
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, Collection $collection)
    {
        return DB::transaction(function () use ($request, $collection) {
            event(EndpointHit::onDelete($request, "Deleted collection persona [{$collection->id}]", $collection));

            $collection->delete();

            return new ResourceDeleted('collection persona');
        });
    }
}
