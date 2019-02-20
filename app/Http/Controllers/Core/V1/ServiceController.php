<?php

namespace App\Http\Controllers\Core\v1;

use App\Events\EndpointHit;
use App\Http\Filters\Service\HasPermissionFilter;
use App\Http\Filters\Service\OrganisationNameFilter;
use App\Http\Requests\Service\DestroyRequest;
use App\Http\Requests\Service\IndexRequest;
use App\Http\Requests\Service\ShowRequest;
use App\Http\Requests\Service\StoreRequest;
use App\Http\Requests\Service\UpdateRequest;
use App\Http\Resources\ServiceResource;
use App\Http\Responses\ResourceDeleted;
use App\Http\Responses\UpdateRequestReceived;
use App\Http\Sorts\Service\OrganisationNameSort;
use App\Models\File;
use App\Models\Service;
use App\Http\Controllers\Controller;
use App\Models\Taxonomy;
use App\Support\MissingValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Sort;

class ServiceController extends Controller
{
    /**
     * ServiceController constructor.
     */
    public function __construct()
    {
        $this->middleware('throttle:60,1');
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
            ->with('serviceCriterion', 'usefulInfos', 'socialMedias', 'taxonomies')
            ->when(auth('api')->guest(), function (Builder $query) use ($request) {
                // Limit to active services if requesting user is not authenticated.
                $query->where('status', '=', Service::STATUS_ACTIVE);
            });

        $services = QueryBuilder::for($baseQuery)
            ->allowedFilters([
                Filter::exact('id'),
                Filter::exact('organisation_id'),
                'name',
                Filter::custom('organisation_name', OrganisationNameFilter::class),
                Filter::exact('status'),
                Filter::exact('referral_method'),
                Filter::custom('has_permission', HasPermissionFilter::class),
            ])
            ->allowedIncludes(['organisation'])
            ->allowedSorts([
                'name',
                Sort::custom('organisation_name', OrganisationNameSort::class),
                'status',
                'referral_method',
            ])
            ->defaultSort('name')
            ->paginate(per_page($request->per_page));

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
            /** @var \App\Models\Service $service */
            $service = Service::create([
                'organisation_id' => $request->organisation_id,
                'slug' => $request->slug,
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
            ]);

            // Upload the logo if provided.
            if ($request->filled('logo')) {
                // Create the file record.
                /** @var \App\Models\File $file */
                $file = File::create([
                    'filename' => $service->id . '.png',
                    'mime_type' => File::MIME_TYPE_PNG,
                    'is_private' => false,
                ]);

                // Upload the file.
                $file->uploadBase64EncodedPng($request->logo);

                // Create resized version for common dimensions.
                foreach (config('ck.cached_image_dimensions') as $maxDimension) {
                    $file->resizedVersion($maxDimension);
                }

                // Link the file to the organisation.
                $service->logo_file_id = $file->id;
                $service->save();
            }

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

            // Ensure conditional fields are reset if needed.
            $service->resetConditionalFields();

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
        $baseQuery = Service::query()
            ->with('serviceCriterion', 'usefulInfos', 'socialMedias', 'taxonomies')
            ->where('id', $service->id);

        $service = QueryBuilder::for($baseQuery)
            ->allowedIncludes(['organisation'])
            ->firstOrFail();

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
            $data = array_filter_missing([
                'slug' => $request->missing('slug'),
                'name' => $request->missing('name'),
                'status' => $request->missing('status'),
                'intro' => $request->missing('intro'),
                'description' => $request->missing('description'),
                'wait_time' => $request->missing('wait_time'),
                'is_free' => $request->missing('is_free'),
                'fees_text' => $request->missing('fees_text'),
                'fees_url' => $request->missing('fees_url'),
                'testimonial' => $request->missing('testimonial'),
                'video_embed' => $request->missing('video_embed'),
                'url' => $request->missing('url'),
                'contact_name' => $request->missing('contact_name'),
                'contact_phone' => $request->missing('contact_phone'),
                'contact_email' => $request->missing('contact_email'),
                'show_referral_disclaimer' => $request->missing('show_referral_disclaimer'),
                'referral_method' => $request->missing('referral_method'),
                'referral_button_text' => $request->missing('referral_button_text'),
                'referral_email' => $request->missing('referral_email'),
                'referral_url' => $request->missing('referral_url'),
                'criteria' => $request->has('criteria')
                    ? array_filter_missing([
                        'age_group' => $request->missing('criteria.age_group'),
                        'disability' => $request->missing('criteria.disability'),
                        'employment' => $request->missing('criteria.employment'),
                        'gender' => $request->missing('criteria.gender'),
                        'housing' => $request->missing('criteria.housing'),
                        'income' => $request->missing('criteria.income'),
                        'language' => $request->missing('criteria.language'),
                        'other' => $request->missing('criteria.other'),
                    ])
                    : new MissingValue(),
                'useful_infos' => $request->has('useful_infos') ? [] : new MissingValue(),
                'social_medias' => $request->has('social_medias') ? [] : new MissingValue(),
                'category_taxonomies' => $request->missing('category_taxonomies'),
            ]);

            // Update the logo if the logo field was provided.
            if ($request->filled('logo')) {
                // If a new logo was uploaded.
                /** @var \App\Models\File $file */
                $file = File::create([
                    'filename' => $service->id.'.png',
                    'mime_type' => File::MIME_TYPE_PNG,
                    'is_private' => false,
                ]);

                // Upload the file.
                $file->uploadBase64EncodedPng($request->logo);

                // Create resized version for common dimensions.
                foreach (config('ck.cached_image_dimensions') as $maxDimension) {
                    $file->resizedVersion($maxDimension);
                }

                $data['logo_file_id'] = $file->id;
            } else if ($request->has('logo')) {
                // If the logo was removed.
                $data['logo_file_id'] = null;
            }

            // Loop through each useful info.
            foreach ($request->input('useful_infos', []) as $usefulInfo) {
                $data['useful_infos'][] = [
                    'title' => $usefulInfo['title'],
                    'description' => $usefulInfo['description'],
                    'order' => $usefulInfo['order'],
                ];
            }

            // Loop through each social media.
            foreach ($request->input('social_medias', []) as $socialMedia) {
                $data['social_medias'][] = [
                    'type' => $socialMedia['type'],
                    'url' => $socialMedia['url'],
                ];
            }

            $updateRequest = $service->updateRequests()->create([
                'user_id' => $request->user()->id,
                'data' => $data,
            ]);

            event(EndpointHit::onUpdate($request, "Updated service [{$service->id}]", $service));

            return new UpdateRequestReceived($updateRequest);
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
