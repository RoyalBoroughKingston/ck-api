<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\Audit\AuditRead;
use App\Events\Audit\AuditsListed;
use App\Http\Requests\Audit\IndexRequest;
use App\Http\Requests\Audit\ShowRequest;
use App\Http\Resources\AuditResource;
use App\Models\Audit;
use App\Http\Controllers\Controller;
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
        $audits = QueryBuilder::for(Audit::class)
            ->allowedFilters(['user_id'])
            ->paginate();

        event(new AuditsListed($request));

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
        event(new AuditRead($request, $audit));

        return new AuditResource($audit);
    }
}
