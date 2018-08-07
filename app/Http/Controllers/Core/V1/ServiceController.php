<?php

namespace App\Http\Controllers\Core\v1;

use App\Events\EndpointHit;
use App\Http\Requests\Service\DestroyRequest;
use App\Http\Requests\Service\IndexRequest;
use App\Http\Requests\Service\ShowRequest;
use App\Http\Requests\Service\StoreRequest;
use App\Http\Requests\Service\UpdateRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class ServiceController extends Controller
{
    /**
     * ServiceController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\Service\IndexRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $baseQuery = Service::query();

        $services = QueryBuilder::for($baseQuery)
            ->allowedFilters(Filter::exact('organisation_id'))
            ->paginate();

        event(EndpointHit::onRead($request, 'Viewed all services'));

        return ServiceResource::collection($services);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Service\StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        return DB::transaction(function () use ($request) {
            // Create the service record.
            $service = Service::create([
                'organisation_id' => $request->organisation_id,
                'name' => $request->name,
                'status' => $request->status,
                'intro' => $request->intro,
                'description' => $request->description,
                'wait_time' => $request->wait_time,
                'is_free' => $request->is_free,
                'fees_text' => $request->fees_text,
                'fees_url' => $request->fees_url,
                'testimonial' => $request->testimonial,
                'video_embed' => $request->video_embed,
                'url' => $request->url,
                'contact_name' => $request->contact_name,
                'contact_phone' => $request->contact_phone,
                'contact_email' => $request->contact_email,
                'show_referral_disclaimer' => $request->show_referral_disclaimer,
                'referral_method' => $request->referral_method,
                'referral_button_text' => $request->referral_button_text,
                'referral_email' => $request->referral_email,
                'referral_url' => $request->referral_url,
                'seo_title' => $request->seo_title,
                'seo_description' => $request->seo_description,
            ]);

            // Create the service criterion record.
            $service->serviceCriterion()->create([
                'age_group' => $request->criteria['age_group'],
                'disability' => $request->criteria['disability'],
                'employment' => $request->criteria['employment'],
                'gender' => $request->criteria['gender'],
                'housing' => $request->criteria['housing'],
                'income' => $request->criteria['income'],
                'language' => $request->criteria['language'],
                'other' => $request->criteria['other'],
            ]);

            // Create the useful info records.
            foreach ($request->useful_infos as $usefulInfo) {
                $service->usefulInfos()->create([
                    'title' => $usefulInfo['title'],
                    'description' => $usefulInfo['description'],
                    'order' => $usefulInfo['order'],
                ]);
            }

            // Create the social media records.
            foreach ($request->social_medias as $socialMedia) {
                $service->socialMedias()->create([
                    'type' => $socialMedia['type'],
                    'url' => $socialMedia['url'],
                ]);
            }

            // Create the category taxonomy records.
            foreach ($request->category_taxonomies as $taxonomy) {
                $service->serviceTaxonomies()->create(['taxonomy_id' => $taxonomy]);
            }

            return new ServiceResource($service);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Service $service
     * @return \Illuminate\Http\Response
     */
    public function show(ShowRequest $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Service $service
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Service $service)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Service $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $service)
    {
        //
    }
}
