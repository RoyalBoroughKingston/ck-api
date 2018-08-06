<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Requests\Notification\IndexRequest;
use App\Http\Requests\Notification\ShowRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;

class NotificationController extends Controller
{
    /**
     * NotificationController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\Notification\IndexRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $notifications = QueryBuilder::for(Notification::class)
            ->allowedFilters('user_id')
            ->paginate();

        event(EndpointHit::onRead($request, 'Viewed all Notifications'));

        return NotificationResource::collection($notifications);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\Notification\ShowRequest $request
     * @param  \App\Models\Notification $notification
     * @return \App\Http\Resources\NotificationResource
     */
    public function show(ShowRequest $request, Notification $notification)
    {
        event(EndpointHit::onRead($request, "Viewed Notification [{$notification->id}]", $notification));

        return new NotificationResource($notification);
    }
}
