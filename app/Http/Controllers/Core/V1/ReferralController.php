<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Requests\Referral\IndexRequest;
use App\Http\Requests\Referral\ShowRequest;
use App\Http\Requests\Referral\StoreRequest;
use App\Http\Requests\Referral\UpdateRequest;
use App\Http\Resources\ReferralResource;
use App\Models\Referral;
use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class ReferralController extends Controller
{
    /**
     * ReferralController constructor.
     */
    public function __construct()
    {
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
        // Constrain the user to only show services that they are a service worker for.
        $userServiceIds = $request
            ->user()
            ->services()
            ->pluck(table(Service::class, 'id'));

        $baseQuery = Referral::query()
            ->whereIn('service_id', $userServiceIds)
            ->orderByDesc('created_at');

        // Filtering by the service ID here will only work for the IDs retrieved above. Others will be discarded.
        $referrals = QueryBuilder::for($baseQuery)
            ->allowedFilters([
                Filter::exact('id'),
                Filter::exact('service_id'),
                Filter::exact('reference'),
            ])
            ->allowedIncludes(['service'])
            ->paginate();

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
            $referral = Referral::create([
                'service_id' => $request->service_id,
                'status' => Referral::STATUS_NEW,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'other_contact' => $request->other_contact,
                'postcode_outward_code' => $request->postcode_outward_code,
                'comments' => $request->comments,
                'referral_consented_at' => $request->referral_consented ? now() : null,
                'feedback_consented_at' => $request->feedback_consented ? now() : null,
                'referee_name' => $request->referee_name,
                'referee_email' => $request->referee_email,
                'referee_phone' => $request->referee_phone,
                'organisation_taxonomy_id' => $request->organisation_taxonomy_id,
                'organisation' => $request->organisation,
            ]);

            event(EndpointHit::onCreate($request, "Created referral [{$referral->id}]", $referral));

            return new ReferralResource($referral);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\Referral\ShowRequest $request
     * @param  \App\Models\Referral $referral
     * @return \App\Http\Resources\ReferralResource
     */
    public function show(ShowRequest $request, Referral $referral)
    {
        event(EndpointHit::onRead($request, "Viewed referral [{$referral->id}]", $referral));

        return new ReferralResource($referral);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Referral\UpdateRequest $request
     * @param  \App\Models\Referral $referral
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Referral $referral)
    {
        return DB::transaction(function () use ($request, $referral) {
            $referral->statusUpdates()->create([
                'user_id' => $request->user()->id,
                'from' => $referral->status,
                'to' => $request->status,
                'comments' => $request->comments,
            ]);

            $referral->update(['status' => $request->status]);

            event(EndpointHit::onUpdate($request, "Updated referral [{$referral->id}]", $referral));

            return new ReferralResource($referral);
        });
    }
}
