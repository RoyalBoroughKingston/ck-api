<?php

namespace App\Http\Controllers\Core\V1;

use App\Geocode\Coordinate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Search\Request;
use App\Search\Search;

class SearchController extends Controller
{
    /**
     * @param \App\Search\Search $search
     * @param \App\Http\Requests\Search\Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function __invoke(Search $search, Request $request)
    {
        // Apply query.
        if ($request->has('query')) {
            $search->applyQuery($request->input('query'));
        }

        if ($request->has('category')) {
            // If category given then filter by category.
            $search->applyCategory($request->category);
        } elseif ($request->has('persona')) {
            // Otherwise, if persona given then filter by persona.
            $search->applyPersona($request->persona);
        }

        // If ordering by distance, then parse the location.
        if ($request->order === 'distance') {
            $location = new Coordinate($request->location['lat'], $request->location['lon']);
        }

        // Apply order.
        $search->applyOrder($request->order ?? 'relevance', $location ?? null);

        // Perform the search.
        return $search->paginate();
    }
}
