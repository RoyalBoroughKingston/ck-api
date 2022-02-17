<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Controllers\Controller;
use App\Http\Requests\Audit\IndexRequest;
use App\Http\Requests\Audit\ShowRequest;
use App\Http\Resources\AuditResource;
use App\Http\Sorts\Audit\UserFullNameSort;
use App\Models\Audit;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class AuditController extends Controller
{
    /**
     * AuditController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\Audit\IndexRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $baseQuery = Audit::query()
            ->with('oauthClient');

        $audits = QueryBuilder::for($baseQuery)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('user_id'),
                AllowedFilter::exact('oauth_client_id'),
                AllowedFilter::exact('action'),
                'description',
            ])
            ->allowedIncludes(['user'])
            ->allowedSorts([
                'action',
                'description',
                AllowedSort::custom('user_full_name', new UserFullNameSort()),
                'created_at',
            ])
            ->defaultSort('-created_at')
            ->paginate(per_page($request->per_page));

        event(EndpointHit::onRead($request, 'Viewed all audits'));

        return AuditResource::collection($audits);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\Audit\ShowRequest $request
     * @param \App\Models\Audit $audit
     * @return \App\Http\Resources\AuditResource
     */
    public function show(ShowRequest $request, Audit $audit)
    {
        $baseQuery = Audit::query()
            ->with('oauthClient')
            ->where('id', $audit->id);

        $audit = QueryBuilder::for($baseQuery)
            ->allowedIncludes(['user'])
            ->firstOrFail();

        event(EndpointHit::onRead($request, "Viewed audit [{$audit->id}]", $audit));

        return new AuditResource($audit);
    }
}
