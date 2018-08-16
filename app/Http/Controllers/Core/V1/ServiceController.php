<?php

namespace App\Http\Controllers\Core\v1;

use App\Events\EndpointHit;
use App\Http\Requests\Service\DestroyRequest;
use App\Http\Requests\Service\IndexRequest;
use App\Http\Requests\Service\ShowRequest;
use App\Http\Requests\Service\StoreRequest;
use App\Http\Requests\Service\UpdateRequest;
use App\Http\Resources\ServiceResource;
use App\Http\Responses\ResourceDeleted;
use App\Http\Responses\UpdateRequestReceived;
use App\Models\Service;
use App\Http\Controllers\Controller;
use App\Models\Taxonomy;
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
        $baseQuery = Service::query()
            ->orderBy('name');

        $services = QueryBuilder::for($baseQuery)
            ->allowedFilters([
                Filter::exact('id'),
                Filter::exact('organisation_id'),
                'name',
            ])
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
            $taxonomies = Taxonomy::whereIn('id', $request->category_taxonomies)->get();
            $service->syncServiceTaxonomies($taxonomies);

            event(EndpointHit::onCreate($request, "Created service [{$service->id}]", $service));

            $service->load('usefulInfos', 'socialMedias', 'taxonomies');

            return new ServiceResource($service);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\Service\ShowRequest $request
     * @param  \App\Models\Service $service
     * @return \App\Http\Resources\ServiceResource
     */
    public function show(ShowRequest $request, Service $service)
    {
        event(EndpointHit::onRead($request, "Viewed service [{$service->id}]", $service));

        return new ServiceResource($service);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Service\UpdateRequest $request
     * @param  \App\Models\Service $service
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Service $service)
    {
        return DB::transaction(function () use ($request, $service) {
            // Initialise the data array.
            $data = [
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
                'criteria' => [
                    'age_group' => $request->criteria['age_group'],
                    'disability' => $request->criteria['disability'],
                    'employment' => $request->criteria['employment'],
                    'gender' => $request->criteria['gender'],
                    'housing' => $request->criteria['housing'],
                    'income' => $request->criteria['income'],
                    'language' => $request->criteria['language'],
                    'other' => $request->criteria['other'],
                ],
                'seo_title' => $request->seo_title,
                'seo_description' => $request->seo_description,
                'useful_infos' => [],
                'social_medias' => [],
                'category_taxonomies' => $request->category_taxonomies,
            ];

            // Loop through each useful info.
            foreach ($request->useful_infos as $usefulInfo) {
                $data['useful_infos'][] = [
                    'title' => $usefulInfo['title'],
                    'description' => $usefulInfo['description'],
                    'order' => $usefulInfo['order'],
                ];
            }

            // Loop through each social media.
            foreach ($request->social_medias as $socialMedia) {
                $data['social_medias'][] = [
                    'type' => $socialMedia['type'],
                    'url' => $socialMedia['url'],
                ];
            }

            $service->updateRequests()->create([
                'user_id' => $request->user()->id,
                'data' => $data,
            ]);

            event(EndpointHit::onUpdate($request, "Updated service [{$service->id}]", $service));

            return new UpdateRequestReceived($data);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\Service\DestroyRequest $request
     * @param  \App\Models\Service $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, Service $service)
    {
        return DB::transaction(function () use ($request, $service) {
            event(EndpointHit::onDelete($request, "Deleted service [{$service->id}]", $service));

            $service->delete();

            return new ResourceDeleted('service');
        });
    }
}
