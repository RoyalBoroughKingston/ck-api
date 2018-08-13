<?php

namespace App\Http\Controllers\Core\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Search\Request;
use App\Http\Resources\ServiceResource;
use App\Models\Service;

class SearchController extends Controller
{
    /**
     * @param \App\Http\Requests\Search\Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function __invoke(Request $request)
    {
        $query = ['query' => ['bool' => []]];

        // TODO: Apply query.
        if ($request->has('query')) {
            $query['query']['bool'] = [
                'should' => [
                    ['match' => ['name' => [
                        'query' => $request->input('query'),
                        'boost' => 4,
                    ]]],
                    ['match' => ['description' => [
                        'query' => $request->input('query'),
                        'boost' => 3,
                    ]]],
                    ['match' => ['taxonomy_categories' => [
                        'query' => $request->input('query'),
                        'boost' => 2,
                    ]]],
                    ['match' => ['organisation_name' => $request->input('query')]],
                ],
            ];
        }

        // TODO: If category given then filter by category.

        // TODO: If persona given then filter by persona.

        // TODO: If order given then apply order (including location).

        // TODO: Apply pagination.

        $services = Service::searchRaw($query);

        return $this->parseToResource($services);
    }

    /**
     * @param array $response
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    protected function parseToResource(array $response)
    {
        $hits = $response['hits']['hits'];

        $serviceIds = array_map(function (array $hit) {
            return $hit['_id'];
        }, $hits);

        $services = Service::query()->whereIn('id', $serviceIds)->paginate();

        return ServiceResource::collection($services);
    }
}
