<?php

namespace App\Search;

use App\Geocode\Coordinate;
use App\Http\Resources\ServiceResource;
use App\Models\SearchHistory;
use App\Models\Service;
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
        $this->query = [];
    }

    /**
     * @param string $term
     * @return \App\Search\Search
     */
    public function applyQuery(string $term): Search
    {
        if (!isset($this->query['query'])) {
            $this->query = ['query' => ['bool' => []]];
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
            $this->query = ['query' => ['bool' => []]];
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
            $this->query = ['query' => ['bool' => []]];
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
                        'locations' => $location->toArray(),
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

        // Get all of the ID's for the service from the hits.
        $serviceIds = array_map(function (array $hit) {
            return $hit['_id'];
        }, $hits);

        // Implode the ID's so we can sort by them in database.
        $serviceIdsImploded = implode("','", $serviceIds);
        $serviceIdsImploded = "'$serviceIdsImploded'";

        // Create the services query.
        $servicesQuery = Service::query()
            ->with('locations')
            ->whereIn('id', $serviceIds)
            ->orderByRaw(DB::raw("FIELD(id,$serviceIdsImploded)"));

        // Either paginate the response or get all.
        $services = $paginate ? $servicesQuery->paginate() : $servicesQuery->get();

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
