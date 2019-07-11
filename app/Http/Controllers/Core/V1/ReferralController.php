<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Controllers\Controller;
use App\Http\Filters\Referral\OrganisationNameFilter;
use App\Http\Filters\Referral\ServiceNameFilter;
use App\Http\Requests\Referral\DestroyRequest;
use App\Http\Requests\Referral\IndexRequest;
use App\Http\Requests\Referral\ShowRequest;
use App\Http\Requests\Referral\StoreRequest;
use App\Http\Requests\Referral\UpdateRequest;
use App\Http\Resources\ReferralResource;
use App\Http\Responses\ResourceDeleted;
use App\Http\Sorts\Referral\OrganisationNameSort;
use App\Http\Sorts\Referral\ServiceNameSort;
use App\Models\Referral;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Sort;

class ReferralController extends Controller
{
    /**
     * ReferralController constructor.
     */
    public function __construct()
    {
        $this->middleware('throttle:60,1');
        $this->middleware('auth:api')->except('store');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\Referral\IndexRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        // Check if the request has asked for the service to be included.
        $serviceIncluded = $request->contains('include', 'service');

        // Constrain the user to only show services that they are a service worker for.
        $userServiceIds = $request
            ->user()
            ->services()
            ->pluck(table(Service::class, 'id'));

        $baseQuery = Referral::query()
            ->select('*')
            ->whereIn('service_id', $userServiceIds)
            ->when($serviceIncluded, function (Builder $query): Builder {
                // If service included, then make sure the service relationships are also eager loaded.
                return $query->with([
                    'service.serviceCriterion',
                    'service.usefulInfos',
                    'service.socialMedias',
                    'service.taxonomies',
                ]);
            });

        // Filtering by the service ID here will only work for the IDs retrieved above. Others will be discarded.
        $referrals = QueryBuilder::for($baseQuery)
            ->allowedFilters([
                Filter::exact('id'),
                Filter::exact('service_id'),
                Filter::exact('reference'),
                Filter::custom('service_name', ServiceNameFilter::class),
                Filter::custom('organisation_name', OrganisationNameFilter::class),
                Filter::exact('status'),
            ])
            ->allowedIncludes(['service.organisation'])
            ->allowedSorts([
                'reference',
                Sort::custom('service_name', ServiceNameSort::class),
                Sort::custom('organisation_name', OrganisationNameSort::class),
                'status',
                'created_at',
            ])
            ->defaultSort('-created_at')
            ->paginate(per_page($request->per_page));

        event(EndpointHit::onRead($request, 'Viewed all referrals'));

        return ReferralResource::collection($referrals);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Referral\StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $referral = new Referral([
                'service_id' => $request->service_id,
                'status' => Referral::STATUS_NEW,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'other_contact' => $request->other_contact,
                'postcode_outward_code' => $request->postcode_outward_code,
                'comments' => $request->comments,
                'referral_consented_at' => $request->referral_consented ? Date::now() : null,
                'feedback_consented_at' => $request->feedback_consented ? Date::now() : null,
            ]);

            // Fill in the fields for client referral.
            if ($request->filled('referee_name')) {
                $referral->fill([
                    'referee_name' => $request->referee_name,
                    'referee_email' => $request->referee_email,
                    'referee_phone' => $request->referee_phone,
                    'organisation_taxonomy_id' => $request->organisation_taxonomy_id,
                    'organisation' => $request->organisation,
                ]);
            }

            $referral->save();

            event(EndpointHit::onCreate($request, "Created referral [{$referral->id}]", $referral));

            return new ReferralResource($referral);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\Referral\ShowRequest $request
     * @param \App\Models\Referral $referral
     * @return \App\Http\Resources\ReferralResource
     */
    public function show(ShowRequest $request, Referral $referral)
    {
        // Check if the request has asked for user roles to be included.
        $serviceIncluded = $request->contains('include', 'service');

        $baseQuery = Referral::query()
            ->select('*')
            ->when($serviceIncluded, function (Builder $query): Builder {
                // If service included, then make sure the service relationships are also eager loaded.
                return $query->with([
                    'service.serviceCriterion',
                    'service.usefulInfos',
                    'service.socialMedias',
                    'service.taxonomies',
                ]);
            })
            ->where('id', $referral->id);

        $referral = QueryBuilder::for($baseQuery)
            ->allowedIncludes('service')
            ->firstOrFail();

        event(EndpointHit::onRead($request, "Viewed referral [{$referral->id}]", $referral));

        return new ReferralResource($referral);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Referral\UpdateRequest $request
     * @param \App\Models\Referral $referral
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Referral $referral)
    {
        return DB::transaction(function () use ($request, $referral) {
            $referral->updateStatus(
                $request->user(),
                $request->status,
                $request->comments
            );

            event(EndpointHit::onUpdate($request, "Updated referral [{$referral->id}]", $referral));

            return new ReferralResource($referral);
        });
    }

    /**
     * @param \App\Http\Requests\Referral\DestroyRequest $request
     * @param \App\Models\Referral $referral
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, Referral $referral)
    {
        return DB::transaction(function () use ($request, $referral) {
            event(EndpointHit::onDelete($request, "Deleted referral [{$referral->id}]", $referral));

            $referral->delete();

            return new ResourceDeleted('referral');
        });
    }
}
