<?php

namespace App\Search;

use App\Geocode\Coordinate;
use App\Http\Resources\ServiceResource;
use App\Models\SearchHistory;
use App\Models\Service;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class Search
{
    /**
     * @var array
     */
    protected $query;

    /**
     * Search constructor.
     */
    public function __construct()
    {
        $this->query = ['size' => config('ck.pagination_results')];
    }

    /**
     * @param string $term
     * @return \App\Search\Search
     */
    public function applyQuery(string $term): Search
    {
        if (!isset($this->query['query'])) {
            $this->query['query'] = ['bool' => []];
        }

        $this->query['query']['bool']['should'] = [
            [
                'match' => [
                    'name' => [
                        'query' => $term,
                        'boost' => 4,
                    ]
                ]
            ],
            [
                'match' => [
                    'description' => [
                        'query' => $term,
                        'boost' => 3,
                    ]
                ]
            ],
            [
                'match' => [
                    'taxonomy_categories' => [
                        'query' => $term,
                        'boost' => 2,
                    ]
                ]
            ],
            [
                'match' => [
                    'organisation_name' => $term,
                ]
            ],
        ];

        return $this;
    }

    /**
     * @param string $category
     * @return \App\Search\Search
     */
    public function applyCategory(string $category): Search
    {
        if (!isset($this->query['query'])) {
            $this->query['query'] = ['bool' => []];
        }

        $this->query['query']['bool']['filter'] = [
            'term' => [
                'collection_categories' => $category
            ]
        ];

        return $this;
    }

    /**
     * @param string $persona
     * @return \App\Search\Search
     */
    public function applyPersona(string $persona): Search
    {
        if (!isset($this->query['query'])) {
            $this->query['query'] = ['bool' => []];
        }

        $this->query['query']['bool']['filter'] = [
            'term' => [
                'collection_personas' => $persona
            ]
        ];

        return $this;
    }

    /**
     * @param string $order
     * @param \App\Geocode\Coordinate|null $location
     * @return \App\Search\Search
     */
    public function applyOrder(string $order, Coordinate $location = null): Search
    {
        if ($order === 'distance') {
            $this->query['sort'] = [
                [
                    '_geo_distance' => [
                        'service_locations.location' => $location->toArray(),
                        'nested_path' => 'service_locations',
                        'distance_type' => 'plane',
                    ]
                ]
            ];
        }

        return $this;
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function paginate()
    {
        $response = Service::searchRaw($this->query);
        $this->logMetrics($response);

        return $this->toResource($response);
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function get()
    {
        $response = Service::searchRaw($this->query);
        $this->logMetrics($response);

        return $this->toResource($response, false);
    }

    /**
     * @param array $response
     * @param bool $paginate
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    protected function toResource(array $response, bool $paginate = true)
    {
        // Extract the hits from the array.
        $hits = $response['hits']['hits'];

        // Get all of the ID's for the services from the hits.
        $serviceIds = collect($hits)->map->_id->toArray();

        // Implode the service ID's so we can sort by them in database.
        $serviceIdsImploded = implode("','", $serviceIds);
        $serviceIdsImploded = "'$serviceIdsImploded'";

        // Get all of the ID's for the service locations from the hits.
        $serviceLocationIds = collect($hits)->flatMap(function (array $hit) {
            return $hit['_source']['service_locations'];
        })->map->id->toArray();

        // Implode the service location ID's so we can sort by them in database.
        $serviceLocationIdsImploded = implode("','", $serviceLocationIds);
        $serviceLocationIdsImploded = "'$serviceLocationIdsImploded'";

        /*
         * Create the query to get the services.
         *
         * Eager load the service locations along with the location they belong to.
         * A constraint has been places on the service locations, to sort them by what was
         * returned by Elasticsearch, as they have been ordered by distance.
         *
         * The services are also ordered by the order returned by Elasticsearch.
         */
        $services = Service::query()
            ->with(['serviceLocations' => function (HasMany $query) use ($serviceLocationIdsImploded) {
                $query->with('location')
                    ->orderByRaw(DB::raw("FIELD(service_locations.id,$serviceLocationIdsImploded)"));
            }])
            ->whereIn('id', $serviceIds)
            ->orderByRaw(DB::raw("FIELD(id,$serviceIdsImploded)"))
            ->get();

        // If paginated, then create a new pagination instance.
        if ($paginate) {
            $services = new LengthAwarePaginator(
                $services,
                $response['hits']['total'],
                config('ck.pagination_results'),
                null,
                ['path' => Paginator::resolveCurrentPath()]
            );
        }

        return ServiceResource::collection($services);
    }

    /**
     * @param array $response
     * @return \App\Search\Search
     */
    protected function logMetrics(array $response): Search
    {
        SearchHistory::create([
            'query' => $this->query,
            'count' => $response['hits']['total'],
        ]);

        return $this;
    }
}
