<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Http\Requests\User\DestroyRequest;
use App\Http\Requests\User\IndexRequest;
use App\Http\Requests\User\ShowRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\Organisation;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\User\IndexRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $baseQuery = User::query()->with('userRoles.service', 'userRoles.organisation');
        $users = QueryBuilder::for($baseQuery)->paginate();

        event(EndpointHit::onRead($request, 'Viewed all users'));

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\User\StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        return DB::transaction(function () use ($request) {
            /** @var \App\Models\User $user */
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => bcrypt($request->password),
            ]);

            foreach ($request->roles as $role) {
                $service = isset($role['service_id'])
                    ? Service::findOrFail($role['service_id'])
                    : null;
                $organisation = isset($role['organisation_id'])
                    ? Organisation::findOrFail($role['organisation_id'])
                    : null;

                switch ($role['role']) {
                    case Role::NAME_SERVICE_WORKER:
                        $user->makeServiceWorker($service);
                        break;
                    case Role::NAME_SERVICE_ADMIN:
                        $user->makeServiceAdmin($service);
                        break;
                    case Role::NAME_ORGANISATION_ADMIN:
                        $user->makeOrganisationAdmin($organisation);
                        break;
                    case Role::NAME_GLOBAL_ADMIN:
                        $user->makeGlobalAdmin();
                        break;
                    case Role::NAME_SUPER_ADMIN:
                        $user->makeSuperAdmin();
                        break;
                }
            }

            return new UserResource($user);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(ShowRequest $request, User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, User $user)
    {
        //
    }
}
