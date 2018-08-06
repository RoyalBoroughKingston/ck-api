<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\Referral\ReferralCreated;
use App\Events\Referral\ReferralRead;
use App\Events\Referral\ReferralsListed;
use App\Http\Requests\Referral\IndexRequest;
use App\Http\Requests\Referral\ShowRequest;
use App\Http\Requests\Referral\StoreRequest;
use App\Http\Requests\Referral\UpdateRequest;
use App\Http\Resources\ReferralResource;
use App\Models\Referral;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
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
        $serviceId = $request->filter['service_id'];
        $baseQuery = Referral::query()->where('service_id', $serviceId);

        $referrals = QueryBuilder::for($baseQuery)->paginate();

        event(new ReferralsListed($request));

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

            event(new ReferralCreated($request, $referral));

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
        event(new ReferralRead($request, $referral));

        return new ReferralResource($referral);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Referral  $referral
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Referral $referral)
    {
        //
    }
}
