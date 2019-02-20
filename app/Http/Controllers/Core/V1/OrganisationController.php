<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Filters\Organisation\HasPermissionFilter;
use App\Http\Requests\Organisation\DestroyRequest;
use App\Http\Requests\Organisation\IndexRequest;
use App\Http\Requests\Organisation\ShowRequest;
use App\Http\Requests\Organisation\StoreRequest;
use App\Http\Requests\Organisation\UpdateRequest;
use App\Http\Resources\OrganisationResource;
use App\Http\Responses\ResourceDeleted;
use App\Http\Responses\UpdateRequestReceived;
use App\Models\File;
use App\Models\Organisation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class OrganisationController extends Controller
{
    /**
     * OrganisationController constructor.
     */
    public function __construct()
    {
        $this->middleware('throttle:60,1');
        $this->middleware('auth:api')->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\Organisation\IndexRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $baseQuery = Organisation::query();

        $organisations = QueryBuilder::for($baseQuery)
            ->allowedFilters([
                Filter::exact('id'),
                'name',
                Filter::custom('has_permission', HasPermissionFilter::class),
            ])
            ->allowedSorts('name')
            ->defaultSort('name')
            ->paginate(per_page($request->per_page));

        event(EndpointHit::onRead($request, 'Viewed all organisations'));

        return OrganisationResource::collection($organisations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Organisation\StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        return DB::transaction(function () use ($request) {
            // Create the organisation.
            $organisation = Organisation::create([
                'slug' => $request->slug,
                'name' => $request->name,
                'description' => $request->description,
                'url' => $request->url,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            // Upload the logo if provided.
            if ($request->filled('logo')) {
                // Create the file record.
                /** @var \App\Models\File $file */
                $file = File::create([
                    'filename' => $organisation->id . '.png',
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
                $organisation->logo_file_id = $file->id;
                $organisation->save();
            }

            event(EndpointHit::onCreate($request, "Created organisation [{$organisation->id}]", $organisation));

            return new OrganisationResource($organisation);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\Organisation\ShowRequest $request
     * @param  \App\Models\Organisation $organisation
     * @return \App\Http\Resources\OrganisationResource
     */
    public function show(ShowRequest $request, Organisation $organisation)
    {
        $baseQuery = Organisation::query()
            ->where('id', $organisation->id);

        $organisation = QueryBuilder::for($baseQuery)
            ->firstOrFail();

        event(EndpointHit::onRead($request, "Viewed organisation [{$organisation->id}]", $organisation));

        return new OrganisationResource($organisation);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Organisation\UpdateRequest $request
     * @param  \App\Models\Organisation $organisation
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Organisation $organisation)
    {
        return DB::transaction(function () use ($request, $organisation) {
            $data = array_filter_missing([
                'slug' => $request->missing('slug'),
                'name' => $request->missing('name'),
                'description' => $request->missing('description'),
                'url' => $request->missing('url'),
                'email' => $request->missing('email'),
                'phone' => $request->missing('phone'),
            ]);

            // Update the logo if the logo field was provided.
            if ($request->filled('logo')) {
                // If a new logo was uploaded.
                /** @var \App\Models\File $file */
                $file = File::create([
                    'filename' => $organisation->id.'.png',
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

            $updateRequest = $organisation->updateRequests()->create([
                'user_id' => $request->user()->id,
                'data' => $data,
            ]);

            event(EndpointHit::onUpdate($request, "Updated organisation [{$organisation->id}]", $organisation));

            return new UpdateRequestReceived($updateRequest);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\Organisation\DestroyRequest $request
     * @param  \App\Models\Organisation $organisation
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, Organisation $organisation)
    {
        return DB::transaction(function () use ($request, $organisation) {
            event(EndpointHit::onDelete($request, "Deleted organisation [{$organisation->id}]", $organisation));

            $organisation->delete();

            return new ResourceDeleted('organisation');
        });
    }
}
