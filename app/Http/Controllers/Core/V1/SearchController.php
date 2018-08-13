<?php

namespace App\Http\Controllers\Core\V1;

use App\Geocode\Coordinate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Search\Request;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * @var array
     */
    protected $query;

    /**
     * SearchController constructor.
     */
    public function __construct()
    {
        $this->query = [
            'query' => [
                'bool' => []
            ]
        ];
    }

    /**
     * @param \App\Http\Requests\Search\Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function __invoke(Request $request)
    {
        // Apply query.
        if ($request->has('query')) {
            $this->applyQuery($request->input('query'));
        }

        if ($request->has('category')) {
            // If category given then filter by category.
            $this->applyCategory($request->category);
        } elseif ($request->has('persona')) {
            // Otherwise, if persona given then filter by persona.
            $this->applyPersona($request->persona);
        }

        if ($request->order === 'location') {
            // If order given then apply order (including location).
            list($lat, $lon) = $request->location;
            $this->applyOrder($request->order, new Coordinate($lat, $lon));
        } else {
            // Default to ordering by relevance.
            $this->applyOrder();
        }

        // Apply pagination.
        $this->applyPagination(15, $request->page ?? 1);

        // Perform the search.
        $services = Service::searchRaw($this->query);

        return $this->parseToResource($services);
    }

    /**
     * @param string $term
     */
    protected function applyQuery(string $term)
    {
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
    }

    /**
     * @param string $category
     */
    protected function applyCategory(string $category)
    {
        $this->query['query']['bool']['filter'] = [
            'term' => [
                'collection_categories' => $category
            ]
        ];
    }

    /**
     * @param string $persona
     */
    protected function applyPersona(string $persona)
    {
        $this->query['query']['bool']['filter'] = [
            'term' => [
                'collection_personas' => $persona
            ]
        ];
    }

    /**
     * @param string $order
     * @param \App\Geocode\Coordinate|null $location
     */
    protected function applyOrder(string $order = 'relevance', Coordinate $location = null)
    {
        // TODO
    }

    /**
     * @param int $perPage
     * @param int $page
     */
    protected function applyPagination(int $perPage, int $page)
    {
        // TODO
    }

    /**
     * @param array $response
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    protected function parseToResource(array $response)
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

        // Paginate all the services returned.
        $services = Service::query()
            ->whereIn('id', $serviceIds)
            ->orderByRaw(DB::raw("FIELD(id,$serviceIdsImploded)"))
            ->paginate();

        return ServiceResource::collection($services);
    }
}