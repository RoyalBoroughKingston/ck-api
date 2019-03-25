<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Requests\Audit\IndexRequest;
use App\Http\Requests\Audit\ShowRequest;
use App\Http\Resources\AuditResource;
use App\Http\Sorts\Audit\UserFullNameSort;
use App\Models\Audit;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Sort;

class AuditController extends Controller
{
    /**
     * AuditController constructor.
     */
    public function __construct()
    {
        $this->middleware('throttle:60,1');
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
                Filter::exact('id'),
                Filter::exact('user_id'),
                Filter::exact('oauth_client_id'),
                Filter::exact('action'),
                'description',
            ])
            ->allowedIncludes(['user'])
            ->allowedSorts([
                'action',
                'description',
                Sort::custom('user_full_name', UserFullNameSort::class),
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
     * @param  \App\Models\Audit $audit
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
