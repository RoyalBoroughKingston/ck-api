<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\Organisation\OrganisationCreated;
use App\Events\Organisation\OrganisationRead;
use App\Events\Organisation\OrganisationsListed;
use App\Http\Requests\Organisation\IndexRequest;
use App\Http\Requests\Organisation\ShowRequest;
use App\Http\Requests\Organisation\StoreRequest;
use App\Http\Resources\OrganisationResource;
use App\Models\Organisation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

class OrganisationController extends Controller
{
    /**
     * OrganisationController constructor.
     */
    public function __construct()
    {
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
        $organisations = QueryBuilder::for(Organisation::class)
            ->paginate();

        event(new OrganisationsListed($request));

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
            $organisation = Organisation::create([
                'name' => $request->name,
                'description' => $request->description,
                'url' => $request->url,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            event(new OrganisationCreated($request, $organisation));

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
        event(new OrganisationRead($request, $organisation));

        return new OrganisationResource($organisation);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Organisation  $organisation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Organisation $organisation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Organisation  $organisation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Organisation $organisation)
    {
        //
    }
}
