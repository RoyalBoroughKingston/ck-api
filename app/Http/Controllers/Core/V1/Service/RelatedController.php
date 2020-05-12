<?php

namespace App\Http\Controllers\Core\V1\Service;

use App\Events\EndpointHit;
use App\Http\Controllers\Controller;
use App\Http\Requests\Service\Related\Request;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Support\Coordinate;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\QueryBuilder;

class RelatedController extends Controller
{
    /**
     * @param \App\Http\Requests\Service\Related\Request $request
     * @param \App\Models\Service $service
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function __invoke(Request $request, Service $service)
    {
        /*
         * This query does the following:
         * 1. Eager load needed relationships.
         * 2. Filter out the service which they are related to.
         * 3. Filter out services with less than 3 taxonomies in common.
         * 4. Order by number of taxonomies in common.
         * 5. Order by distance (if location provided in request).
         * 6. Order by service name.
         */
        $baseQuery = Service::query()
            ->where('services.status', '=', Service::STATUS_ACTIVE)
            ->with('serviceCriterion', 'usefulInfos', 'socialMedias', 'serviceGalleryItems.file', 'taxonomies')
            ->where('services.id', '!=', $service->id)
            ->whereHas('serviceTaxonomies', function (Builder $query) use ($service) {
                $query->whereIn(
                    'service_taxonomies.taxonomy_id',
                    $service->taxonomies()->pluck('taxonomies.id')
                );
            }, '>=', 3)
            ->orderByRaw(
                "(SELECT COUNT(*) FROM `service_taxonomies` WHERE `service_taxonomies`.`taxonomy_id` IN ('?')) DESC",
                $service->taxonomies()->pluck('taxonomies.id')->implode("','")
            )
            ->when($request->has('location'), function (Builder $query) use ($request) {
                $location = new Coordinate(
                    $request->input('location.lat'),
                    $request->input('location.lon')
                );

                $sql = '(ACOS(
                    COS(RADIANS(?)) * 
                    COS(RADIANS(`services`.`lat`)) * 
                    COS(RADIANS(`services`.`lon`) - RADIANS(?)) + 
                    SIN(RADIANS(?)) * 
                    SIN(RADIANS(`services`.`lat`)) 
                ))';

                $query->orderByRaw($sql, [$location->lat(), $location->lon(), $location->lat()]);
            })
            ->orderBy('services.name');

        $services = QueryBuilder::for($baseQuery)
            ->allowedIncludes(['organisation'])
            ->paginate(per_page($request->per_page));

        event(EndpointHit::onRead($request, "Viewed all related services [{$service->id}]"));

        return ServiceResource::collection($services);
    }
}
